<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Course;
use App\Models\Mentor;
use App\Models\Message;
use App\Models\Opportunity;
use App\Models\OpportunityApplication;
use App\Models\PartnershipRequest;
use App\Models\Payment;
use App\Models\Publication;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $user = auth()->user();

        $canUsers = $user?->can('users.index') || $user?->can('user_management.mentor.index') || $user?->can('user_management.researcher.index') || $user?->can('user_management.coordinator.index');
        $canCourses = $user?->can('admin.courses.index') || $user?->can('admin.modules.index') || $user?->can('admin.lessons.index');
        $canOpportunities = $user?->can('admin.opportunities.index') || $user?->can('admin.opportunity_applications.index');
        $canContent = $user?->can('admin.services.index') || $user?->can('admin.publications.index');
        $canPayments = $user?->can('admin.payments.index');
        $canChats = $user?->can('admin.opportunity_chats.index');
        $canPartnership = $user?->can('admin.partnership_requests.index');

        $monthStart = Carbon::now()->startOfMonth();

        $totalUsers = $canUsers ? User::query()->count() : null;
        $activeUsers = $canUsers ? User::query()->where('status', 'active')->count() : null;
        $usersThisMonth = $canUsers ? User::query()->where('created_at', '>=', $monthStart)->count() : null;
        $totalMentors = $canUsers ? Mentor::query()->count() : null;
        $totalResearchers = $canUsers ? User::query()->where('role', 'researcher')->count() : null;
        $totalCoordinators = $canUsers ? User::query()->where('role', 'coordinator')->count() : null;

        $totalCourses = $canCourses ? Course::query()->count() : null;
        $activeCourses = $canCourses ? Course::query()->where('status', 'active')->count() : null;
        $inactiveCourses = $canCourses ? Course::query()->where('status', 'inactive')->count() : null;
        $coursesThisMonth = $canCourses ? Course::query()->where('created_at', '>=', $monthStart)->count() : null;

        $totalOpportunities = $canOpportunities ? Opportunity::query()->count() : null;
        $activeOpportunities = $canOpportunities ? Opportunity::query()->where('status', 'active')->count() : null;
        $inactiveOpportunities = $canOpportunities ? Opportunity::query()->where('status', 'inactive')->count() : null;
        $opportunitiesThisMonth = $canOpportunities ? Opportunity::query()->where('created_at', '>=', $monthStart)->count() : null;

        $pendingApplications = $canOpportunities
            ? OpportunityApplication::query()->where('status', 'pending')->count()
            : null;

        $totalServices = $user?->can('admin.services.index') ? Service::query()->count() : null;
        $totalPublications = $user?->can('admin.publications.index') ? Publication::query()->count() : null;

        $totalPayments = $canPayments ? Payment::query()->count() : null;
        $pendingPayments = $canPayments ? Payment::query()->where('status', 'pending')->count() : null;
        $paymentStatusCounts = $canPayments
            ? Payment::query()
                ->select('status', DB::raw('COUNT(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray()
            : [];

        $totalChats = $canChats ? Chat::query()->count() : null;
        $activeChats = $canChats ? Chat::query()->where('status', 'active')->count() : null;
        $inactiveChats = $canChats ? Chat::query()->where('status', 'inactive')->count() : null;
        $groupChats = $canChats ? Chat::query()->where('type', 'group')->count() : null;
        $singleChats = $canChats ? Chat::query()->where('type', 'single')->count() : null;

        $totalMessages = $canChats ? Message::query()->count() : null;
        $messagesLast7 = $canChats
            ? Message::query()->where('created_at', '>=', Carbon::now()->subDays(7))->count()
            : null;

        $pendingPartnership = $canPartnership
            ? PartnershipRequest::query()->where('status', 'pending')->count()
            : null;

        $metrics = [];
        $chatPie = null;
        $chatTypePie = null;
        $growthChart = null;

        if ($canChats) {
            $metricStart = Carbon::today()->subDays(6);
            $messageCounts = Message::query()
                ->select(DB::raw('DATE(created_at) as day'), DB::raw('COUNT(*) as total'))
                ->where('created_at', '>=', $metricStart->copy()->startOfDay())
                ->groupBy(DB::raw('DATE(created_at)'))
                ->pluck('total', 'day')
                ->toArray();

            $metricLabels = [];
            $metricSeries = [];
            for ($i = 0; $i < 7; $i++) {
                $d = $metricStart->copy()->addDays($i);
                $key = $d->toDateString();
                $metricLabels[] = $d->format('D');
                $metricSeries[] = (int) ($messageCounts[$key] ?? 0);
            }

            $metrics['messages_last_7_days'] = [
                'labels' => $metricLabels,
                'series' => $metricSeries,
            ];

            $chatPie = [
                'labels' => [__('admin.status.active'), __('admin.status.inactive')],
                'series' => [(int) $activeChats, (int) $inactiveChats],
            ];

            $chatTypePie = [
                'labels' => [__('admin.status.group'), __('admin.status.single')],
                'series' => [(int) $groupChats, (int) $singleChats],
            ];
        }

        if ($canUsers || $canCourses || $canOpportunities) {
            $growthChart = $this->buildGrowthChart($canUsers, $canCourses, $canOpportunities);
        }

        $statusBars = [];
        if ($canCourses) {
            $statusBars['courses'] = [
                'labels' => [__('admin.status.active'), __('admin.status.inactive')],
                'series' => [(int) $activeCourses, (int) $inactiveCourses],
            ];
        }
        if ($canOpportunities) {
            $statusBars['opportunities'] = [
                'labels' => [__('admin.status.active'), __('admin.status.inactive')],
                'series' => [(int) $activeOpportunities, (int) $inactiveOpportunities],
            ];
        }
        if ($canPayments) {
            $order = ['pending', 'paid', 'failed', 'cancelled', 'refunded'];
            $labels = [];
            $series = [];
            foreach ($order as $st) {
                $labels[] = __('admin.status.' . $st);
                $series[] = (int) ($paymentStatusCounts[$st] ?? 0);
            }
            $statusBars['payments'] = [
                'labels' => $labels,
                'series' => $series,
            ];
        }

        $cards = [];
        if ($canUsers) {
            $cards['users'] = [
                'total' => $totalUsers,
                'active' => $activeUsers,
                'this_month' => $usersThisMonth,
                'mentors' => $totalMentors,
                'researchers' => $totalResearchers,
                'coordinators' => $totalCoordinators,
            ];
        }
        if ($canCourses) {
            $cards['courses'] = [
                'total' => $totalCourses,
                'active' => $activeCourses,
                'inactive' => $inactiveCourses,
                'this_month' => $coursesThisMonth,
            ];
        }
        if ($canOpportunities) {
            $cards['opportunities'] = [
                'total' => $totalOpportunities,
                'active' => $activeOpportunities,
                'inactive' => $inactiveOpportunities,
                'this_month' => $opportunitiesThisMonth,
                'pending_applications' => $pendingApplications,
            ];
        }
        if ($canContent) {
            $cards['content'] = [
                'services' => $totalServices,
                'publications' => $totalPublications,
            ];
        }
        if ($canPayments) {
            $cards['payments'] = [
                'total' => $totalPayments,
                'pending' => $pendingPayments,
            ];
        }
        if ($canChats) {
            $cards['chats'] = [
                'total' => $totalChats,
                'active' => $activeChats,
                'messages' => $totalMessages,
                'messages_last_7_days' => $messagesLast7,
            ];
        }

        $heroPills = array_filter([
            'users' => $canUsers ? $totalUsers : null,
            'opportunities' => $canOpportunities ? $totalOpportunities : null,
            'courses' => $canCourses ? $totalCourses : null,
            'pending' => $pendingApplications ?? $pendingPartnership,
        ], fn ($v) => $v !== null);

        $recentUsers = $canUsers
            ? User::query()->latest()->limit(5)->get(['id', 'name', 'email', 'role', 'status', 'created_at'])
            : collect();

        $recentOpportunities = $canOpportunities
            ? Opportunity::query()->latest()->limit(5)->get(['id', 'title', 'status', 'created_at'])
            : collect();

        $recentApplications = $canOpportunities
            ? OpportunityApplication::query()->with('opportunity:id,title')->latest()->limit(5)->get()
            : collect();

        $quickLinks = $this->quickLinks($user);

        return view('backend.layouts.dashboard.index', compact(
            'cards',
            'chatPie',
            'chatTypePie',
            'metrics',
            'statusBars',
            'growthChart',
            'heroPills',
            'recentUsers',
            'recentOpportunities',
            'recentApplications',
            'quickLinks',
            'pendingPartnership',
        ));
    }

    private function buildGrowthChart(bool $users, bool $courses, bool $opportunities): array
    {
        $labels = [];
        $userSeries = [];
        $courseSeries = [];
        $opportunitySeries = [];

        for ($i = 5; $i >= 0; $i--) {
            $start = Carbon::now()->subMonths($i)->startOfMonth();
            $end = $start->copy()->endOfMonth();
            $labels[] = $start->format('M');

            $userSeries[] = $users
                ? User::query()->whereBetween('created_at', [$start, $end])->count()
                : 0;
            $courseSeries[] = $courses
                ? Course::query()->whereBetween('created_at', [$start, $end])->count()
                : 0;
            $opportunitySeries[] = $opportunities
                ? Opportunity::query()->whereBetween('created_at', [$start, $end])->count()
                : 0;
        }

        return [
            'labels' => $labels,
            'users' => $userSeries,
            'courses' => $courseSeries,
            'opportunities' => $opportunitySeries,
        ];
    }

    private function quickLinks($user): array
    {
        $links = [];

        if ($user?->can('admin.opportunities.index')) {
            $links[] = ['label' => __('admin.menu.opportunities'), 'route' => route('admin.opportunities.index'), 'icon' => 'work'];
        }
        if ($user?->can('admin.courses.index')) {
            $links[] = ['label' => __('admin.menu.courses'), 'route' => route('admin.courses.index'), 'icon' => 'school'];
        }
        if ($user?->can('admin.opportunity_chats.index')) {
            $links[] = ['label' => __('admin.dash.opportunity_chats_link'), 'route' => route('admin.opportunity_chats.index'), 'icon' => 'forum'];
        }
        if ($user?->can('admin.opportunity_applications.index')) {
            $links[] = ['label' => __('admin.dash.applications'), 'route' => route('admin.opportunity_applications.index'), 'icon' => 'assignment'];
        }
        if ($user?->can('admin.partnership_requests.index')) {
            $links[] = ['label' => __('admin.menu.partnership_requests'), 'route' => route('admin.partnership_requests.index'), 'icon' => 'handshake'];
        }
        if ($user?->can('admin.offers.index')) {
            $links[] = ['label' => __('admin.menu.offers'), 'route' => route('admin.offers.index'), 'icon' => 'local_offer'];
        }

        return $links;
    }
}
