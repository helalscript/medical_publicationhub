<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Course;
use App\Models\Mentor;
use App\Models\Message;
use App\Models\Opportunity;
use App\Models\Publication;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $totalUsers = User::query()->count();
        $totalMentors = Mentor::query()->count();
        $totalResearchers = User::query()->where('role', 'researcher')->count();
        $totalCoordinators = User::query()->where('role', 'coordinator')->count();

        $totalCourses = Course::query()->count();
        $activeCourses = Course::query()->where('status', 'active')->count();

        $totalOpportunities = Opportunity::query()->count();
        $activeOpportunities = Opportunity::query()->where('status', 'active')->count();

        $totalServices = Service::query()->count();
        $totalPublications = Publication::query()->count();

        $totalChats = Chat::query()->count();
        $activeChats = Chat::query()->where('status', 'active')->count();
        $inactiveChats = Chat::query()->where('status', 'inactive')->count();
        $groupChats = Chat::query()->where('type', 'group')->count();
        $singleChats = Chat::query()->where('type', 'single')->count();

        $totalMessages = Message::query()->count();
        $messagesLast7 = Message::query()
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->count();

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

        $metrics = [
            'messages_last_7_days' => [
                'labels' => $metricLabels,
                'series' => $metricSeries,
            ],
        ];

        $chatPie = [
            'labels' => ['Active', 'Inactive'],
            'series' => [
                (int) $activeChats,
                (int) $inactiveChats,
            ],
        ];

        $chatTypePie = [
            'labels' => ['Group', 'Single'],
            'series' => [
                (int) $groupChats,
                (int) $singleChats,
            ],
        ];

        $cards = [
            'users' => [
                'total' => $totalUsers,
                'mentors' => $totalMentors,
                'researchers' => $totalResearchers,
                'coordinators' => $totalCoordinators,
            ],
            'courses' => [
                'total' => $totalCourses,
                'active' => $activeCourses,
            ],
            'opportunities' => [
                'total' => $totalOpportunities,
                'active' => $activeOpportunities,
            ],
            'content' => [
                'services' => $totalServices,
                'publications' => $totalPublications,
            ],
            'chats' => [
                'total' => $totalChats,
                'messages' => $totalMessages,
                'messages_last_7_days' => $messagesLast7,
            ],
        ];

        return view('backend.layouts.dashboard.index', compact(
            'cards',
            'chatPie',
            'chatTypePie',
            'metrics'
        ));
    }
}
