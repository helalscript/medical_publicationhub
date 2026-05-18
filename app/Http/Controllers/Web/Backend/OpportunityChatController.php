<?php

namespace App\Http\Controllers\Web\Backend;

use App\Events\MessageSent;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Chat;
use App\Models\ChatUser;
use App\Models\Message;
use App\Models\Opportunity;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class OpportunityChatController extends Controller
{
    public function index(Request $request)
    {
        if ($request->boolean('export')) {
            return $this->exportChats($request);
        }

        if ($request->ajax()) {
            $query = $this->opportunityChatsQuery()
                ->with(['chatable', 'users.user', 'last_message.user'])
                ->withCount(['users', 'messages'])
                ->latest('id');

            $this->applyEngagementFilter($query, $request->get('engagement_filter', 'all'));

            return DataTables::of($query)
                ->filterColumn('chatable.title', function ($query, $keyword) {
                    $query->whereHasMorph('chatable', [Opportunity::class], function (Builder $q) use ($keyword) {
                        $q->where('title', 'like', '%'.$keyword.'%');
                    });
                })
                ->addIndexColumn()
                ->addColumn('opportunity', function ($row) {
                    $title = e($row->chatable?->title ?? '-');
                    $subtitle = e($this->opportunitySubtitle($row->chatable));

                    return '<div class="oc-opportunity-cell">'
                        .'<span class="oc-opportunity-title">'.$title.'</span>'
                        .'<span class="oc-opportunity-subtitle">'.$subtitle.'</span>'
                        .'</div>';
                })
                ->addColumn('participants', fn ($row) => $this->participantsColumnHtml($row))
                ->addColumn('activity', fn ($row) => $this->activityColumnHtml($row))
                ->addColumn('last_message', fn ($row) => $this->lastMessageColumnHtml($row))
                ->addColumn('engagement_status', fn ($row) => $this->engagementStatusBadgeHtml($row))
                ->addColumn('action', fn ($row) => $this->actionColumnHtml($row))
                ->rawColumns(['opportunity', 'participants', 'activity', 'last_message', 'engagement_status', 'action'])
                ->make(true);
        }

        $stats = $this->buildIndexStats();

        return view('backend.layouts.opportunity_chats.index', compact('stats'));
    }

    public function show(Request $request, string $id)
    {
        $chat = Chat::with(['chatable.mentor.user', 'users.user'])->findOrFail($id);
        $messagesQuery = Message::with('user')
            ->where('chat_id', $chat->id)
            ->latest('id');

        if ($request->ajax()) {
            return response()->json($messagesQuery->paginate(10));
        }

        $messages = $messagesQuery->paginate(10);

        return view('backend.layouts.opportunity_chats.show', compact('chat', 'messages'));
    }

    public function sendMessage(Request $request, string $id)
    {
        $request->validate([
            'message' => 'required_without:file|nullable|string|max:10000',
            'file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,gif,mp3,wav,ogg,m4a,aac,webm|max:10240',
        ]);
        try {
            $chat = Chat::findOrFail($id);
            ChatUser::firstOrCreate(
                ['chat_id' => $chat->id, 'user_id' => (int) $request->user()->id],
                ['role' => 'super_admin', 'status' => 'active']
            );
            $msg = Message::create([
                'chat_id' => $chat->id,
                'user_id' => $request->user()->id,
                'message' => $request->input('message'),
                'file' => $request->hasFile('file') ? Helper::fileUpload($request->file('file'), 'chats', 'chat-'.time()) : null,
            ]);

            event(new MessageSent($msg->load('user')));

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => $msg]);
            }

            return redirect()->back()->with('success', 'Message sent.');
        } catch (Exception $e) {
            Log::error('OpportunityChatController::sendMessage '.$e->getMessage());
            if ($request->ajax()) {
                return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
            }

            return redirect()->back()->withInput()->with('error', 'Failed to send message.');
        }
    }

    private function exportChats(Request $request)
    {
        $query = $this->opportunityChatsQuery()
            ->with(['chatable', 'users.user', 'last_message'])
            ->withCount(['users', 'messages'])
            ->latest('id');

        $this->applyEngagementFilter($query, $request->get('engagement_filter', 'all'));

        if ($search = $request->get('search')) {
            $query->whereHasMorph('chatable', [Opportunity::class], function (Builder $q) use ($search) {
                $q->where('title', 'like', '%'.$search.'%');
            });
        }

        $filename = 'opportunity-chats-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['#', 'Opportunity', 'Study design', 'Participants', 'Activity %', 'Last message', 'Status']);

            foreach ($query->cursor() as $index => $chat) {
                $last = $chat->last_message;
                $engagement = $this->chatEngagement($chat);
                $lastLabel = $last
                    ? ($engagement === 'active' ? Str::limit(strip_tags($last->message ?: 'Attachment'), 80) : 'No activity')
                    : 'No activity';

                fputcsv($handle, [
                    $index + 1,
                    $chat->chatable?->title ?? '-',
                    $this->opportunitySubtitle($chat->chatable),
                    (int) $chat->users_count,
                    $this->activityPercent($chat),
                    $last ? $lastLabel.' ('.$last->created_at?->diffForHumans().')' : '—',
                    ucfirst($engagement),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    private function opportunityChatsQuery(): Builder
    {
        return Chat::query()->where('chatable_type', Opportunity::class);
    }

    private function lastMessageAtSubquery(): string
    {
        return '(SELECT MAX(created_at) FROM messages WHERE messages.chat_id = chats.id AND messages.deleted_at IS NULL)';
    }

    private function messageCountSubquery(): string
    {
        return '(SELECT COUNT(*) FROM messages WHERE messages.chat_id = chats.id AND messages.deleted_at IS NULL)';
    }

    private function resolveEngagementStatus(?string $lastMessageAt, int $messageCount): string
    {
        if ($messageCount === 0 || $lastMessageAt === null) {
            return 'silent';
        }

        $last = Carbon::parse($lastMessageAt);

        if ($last->gte(now()->subHours(48))) {
            return 'active';
        }

        if ($last->gte(now()->subDays(5))) {
            return 'idle';
        }

        return 'silent';
    }

    private function applyEngagementFilter(Builder $query, ?string $filter): void
    {
        if (! $filter || $filter === 'all') {
            return;
        }

        $lastAt = $this->lastMessageAtSubquery();
        $msgCount = $this->messageCountSubquery();

        match ($filter) {
            'active' => $query->whereRaw("{$lastAt} >= ?", [now()->subHours(48)]),
            'idle' => $query->whereRaw("{$msgCount} > 0")
                ->whereRaw("({$lastAt} < ? OR {$lastAt} IS NULL)", [now()->subHours(48)])
                ->whereRaw("{$lastAt} >= ?", [now()->subDays(5)]),
            'silent' => $query->where(function (Builder $q) use ($lastAt, $msgCount) {
                $q->whereRaw("{$msgCount} = 0")
                    ->orWhereRaw("{$lastAt} IS NULL")
                    ->orWhereRaw("{$lastAt} < ?", [now()->subDays(5)]);
            }),
            default => null,
        };
    }

    private function buildIndexStats(): array
    {
        $base = $this->opportunityChatsQuery();
        $total = (clone $base)->count();
        $thisWeek = (clone $base)->where('created_at', '>=', now()->startOfWeek())->count();

        $lastAt = $this->lastMessageAtSubquery();
        $msgCount = $this->messageCountSubquery();

        $activeNow = (clone $base)->whereRaw("{$lastAt} >= ?", [now()->subHours(48)])->count();

        $idleChats = (clone $base)
            ->whereRaw("{$msgCount} > 0")
            ->whereRaw("({$lastAt} < ? OR {$lastAt} IS NULL)", [now()->subHours(48)])
            ->whereRaw("{$lastAt} >= ?", [now()->subDays(5)])
            ->count();

        $totalParticipants = ChatUser::query()
            ->whereIn('chat_id', (clone $base)->select('id'))
            ->count();

        $engagementPercent = $total > 0 ? (int) round(($activeNow / $total) * 100) : 0;

        return [
            'total' => $total,
            'this_week' => $thisWeek,
            'active_now' => $activeNow,
            'idle' => $idleChats,
            'engagement_percent' => $engagementPercent,
            'total_participants' => $totalParticipants,
        ];
    }

    /** @var array<int, string> */
    private static array $categoryNameCache = [];

    private function opportunitySubtitle(?Opportunity $opportunity): string
    {
        if (! $opportunity) {
            return '-';
        }

        $ids = $opportunity->studydesign_type_ids ?? [];
        if (! is_array($ids) || count($ids) === 0) {
            return '-';
        }

        $id = (int) $ids[0];
        if (! isset(self::$categoryNameCache[$id])) {
            self::$categoryNameCache[$id] = Category::query()->whereKey($id)->value('name') ?? '-';
        }

        return self::$categoryNameCache[$id];
    }

    private function chatEngagement(Chat $chat): string
    {
        $lastAt = $chat->last_message?->created_at?->toDateTimeString();

        return $this->resolveEngagementStatus($lastAt, (int) $chat->messages_count);
    }

    private function activityPercent(Chat $chat): int
    {
        $participantCount = max((int) $chat->users_count, 1);

        $activeSenders = (int) Message::query()
            ->where('chat_id', $chat->id)
            ->where('created_at', '>=', now()->subDays(7))
            ->selectRaw('COUNT(DISTINCT user_id) as aggregate')
            ->value('aggregate');

        return min(100, (int) round(($activeSenders / $participantCount) * 100));
    }

    private function participantsColumnHtml(Chat $chat): string
    {
        $participants = $chat->users->map(fn ($cu) => $cu->user)->filter();
        $count = $participants->count();

        if ($count === 0) {
            return '<span class="text-muted small">0</span>';
        }

        $bgColors = ['#e0f2fe', '#dcfce7', '#fef3c7', '#fce7f3', '#ede9fe'];
        $fgColors = ['#0369a1', '#15803d', '#a16207', '#be185d', '#6d28d9'];

        $html = '<div class="oc-participants">';

        foreach ($participants->take(3) as $index => $user) {
            $initial = strtoupper(mb_substr($user->name ?? '?', 0, 1));
            if ($user->avatar) {
                $html .= '<img src="'.e(asset($user->avatar)).'" alt="" class="oc-participant-avatar" title="'.e($user->name).'">';
            } else {
                $bg = $bgColors[$index % count($bgColors)];
                $fg = $fgColors[$index % count($fgColors)];
                $html .= '<span class="oc-participant-initial" style="background:'.$bg.';color:'.$fg.'" title="'.e($user->name).'">'.$initial.'</span>';
            }
        }

        $html .= '<span class="oc-participant-count">'.$count.'</span></div>';

        return $html;
    }

    private function activityColumnHtml(Chat $chat): string
    {
        $percent = $this->activityPercent($chat);

        return '<div class="oc-activity">'
            .'<div class="oc-activity-bar"><span style="width:'.$percent.'%"></span></div>'
            .'<span class="oc-activity-value">'.$percent.'%</span>'
            .'</div>';
    }

    private function lastMessageColumnHtml(Chat $chat): string
    {
        $engagement = $this->chatEngagement($chat);
        $last = $chat->last_message;

        if (! $last) {
            return '<div class="oc-last-message"><span class="oc-last-message-title">No activity</span><span class="oc-last-message-sub">—</span></div>';
        }

        $ago = $last->created_at?->diffForHumans(short: true) ?? '—';

        if ($engagement === 'active') {
            $preview = $last->message
                ? Str::limit(strip_tags($last->message), 36)
                : ($last->file ? 'Attachment' : 'Message');

            return '<div class="oc-last-message">'
                .'<span class="oc-last-message-title">'.e($preview).'</span>'
                .'<span class="oc-last-message-sub">'.e($ago).'</span>'
                .'</div>';
        }

        return '<div class="oc-last-message">'
            .'<span class="oc-last-message-title">No activity</span>'
            .'<span class="oc-last-message-sub">'.e($ago).'</span>'
            .'</div>';
    }

    private function engagementStatusBadgeHtml(Chat $chat): string
    {
        $status = $this->chatEngagement($chat);

        $classes = match ($status) {
            'active' => 'oc-status-active',
            'idle' => 'oc-status-idle',
            default => 'oc-status-silent',
        };

        $label = ucfirst($status);

        return '<span class="oc-status-badge '.$classes.'">'.e($label).'</span>';
    }

    private function actionColumnHtml(Chat $chat): string
    {
        $showUrl = route('admin.opportunity_chats.show', $chat->id);
        $opportunityId = $chat->chatable_id;
        $opportunityUrl = $opportunityId
            ? route('admin.opportunities.show', $opportunityId)
            : '#';

        return '<div class="oc-actions">'
            .'<a href="'.$showUrl.'" class="oc-action-btn" title="View"><i class="material-symbols-outlined fs-16">visibility</i></a>'
            .'<a href="'.$showUrl.'" class="oc-action-btn" title="Message"><i class="material-symbols-outlined fs-16">chat</i></a>'
            .'<div class="dropdown">'
            .'<button type="button" class="oc-action-btn" data-bs-toggle="dropdown" aria-expanded="false" title="More"><i class="material-symbols-outlined fs-16">more_horiz</i></button>'
            .'<ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">'
            .($opportunityId ? '<li><a class="dropdown-item" href="'.$opportunityUrl.'">View opportunity</a></li>' : '')
            .'<li><a class="dropdown-item" href="'.$showUrl.'">Open chat</a></li>'
            .'</ul>'
            .'</div>'
            .'</div>';
    }
}
