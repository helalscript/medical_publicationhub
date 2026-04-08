<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Course;
use App\Models\Mentor;
use App\Models\Message;
use App\Models\Opportunity;
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

        $totalUsers = $canUsers ? User::query()->count() : null;
        $totalMentors = $canUsers ? Mentor::query()->count() : null;
        $totalResearchers = $canUsers ? User::query()->where('role', 'researcher')->count() : null;
        $totalCoordinators = $canUsers ? User::query()->where('role', 'coordinator')->count() : null;

        $totalCourses = $canCourses ? Course::query()->count() : null;
        $activeCourses = $canCourses ? Course::query()->where('status', 'active')->count() : null;
        $inactiveCourses = $canCourses ? Course::query()->where('status', 'inactive')->count() : null;

        $totalOpportunities = $canOpportunities ? Opportunity::query()->count() : null;
        $activeOpportunities = $canOpportunities ? Opportunity::query()->where('status', 'active')->count() : null;
        $inactiveOpportunities = $canOpportunities ? Opportunity::query()->where('status', 'inactive')->count() : null;

        $totalServices = $user?->can('admin.services.index') ? Service::query()->count() : null;
        $totalPublications = $user?->can('admin.publications.index') ? Publication::query()->count() : null;

        $totalPayments = $canPayments ? Payment::query()->count() : null;
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

        $metrics = [];
        $chatPie = null;
        $chatTypePie = null;

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
                'labels' => ['Active', 'Inactive'],
                'series' => [(int) $activeChats, (int) $inactiveChats],
            ];
        }

        $statusBars = [];
        if ($canCourses) {
            $statusBars['courses'] = [
                'labels' => ['Active', 'Inactive'],
                'series' => [(int) $activeCourses, (int) $inactiveCourses],
            ];
        }
        if ($canOpportunities) {
            $statusBars['opportunities'] = [
                'labels' => ['Active', 'Inactive'],
                'series' => [(int) $activeOpportunities, (int) $inactiveOpportunities],
            ];
        }
        if ($canPayments) {
            $order = ['pending', 'paid', 'failed', 'cancelled', 'refunded'];
            $labels = [];
            $series = [];
            foreach ($order as $st) {
                $labels[] = ucfirst($st);
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
                'mentors' => $totalMentors,
                'researchers' => $totalResearchers,
                'coordinators' => $totalCoordinators,
            ];
        }
        if ($canCourses) {
            $cards['courses'] = [
                'total' => $totalCourses,
                'active' => $activeCourses,
            ];
        }
        if ($canOpportunities) {
            $cards['opportunities'] = [
                'total' => $totalOpportunities,
                'active' => $activeOpportunities,
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
            ];
        }
        if ($canChats) {
            $cards['chats'] = [
                'total' => $totalChats,
                'messages' => $totalMessages,
                'messages_last_7_days' => $messagesLast7,
            ];
        }

        return view('backend.layouts.dashboard.index', compact(
            'cards',
            'chatPie',
            'chatTypePie',
            'metrics',
            'statusBars'
        ));
    }
}
