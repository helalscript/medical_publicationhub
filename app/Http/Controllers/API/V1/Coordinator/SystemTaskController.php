<?php

namespace App\Http\Controllers\API\V1\Coordinator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SystemTaskController extends Controller
{
    public function overview()
    {
        $totalResearchers = \App\Models\User::where('role', 'researcher')->count();
        $totalMentors = \App\Models\User::where('role', 'mentor')->count();
        $activeTasks = \App\Models\Task::where('deadline', '>', now())->count();
        
        $totalSubmissions = \App\Models\Submission::count();
        $completedSubmissions = \App\Models\AiFeedback::where('status', 'approved')->count();
        
        $completionRate = $totalSubmissions > 0 ? round(($completedSubmissions / $totalSubmissions) * 100, 2) : 0;
        
        $avgScore = \App\Models\AiFeedback::where('status', 'approved')->avg('overall_score') ?? 0;

        return response()->json([
            'data' => [
                'total_researchers' => $totalResearchers,
                'total_mentors' => $totalMentors,
                'active_tasks' => $activeTasks,
                'completion_rate' => $completionRate,
                'avg_score' => round($avgScore, 2)
            ]
        ], 200);
    }

    public function groups()
    {
        // Assuming groups are handled via a Group model or User roles
        return response()->json(['message' => 'Groups list placeholder'], 200);
    }

    public function tasks(Request $request)
    {
        $query = \App\Models\Task::with(['mentor', 'rubric']);
        
        if ($request->has('mentor_id')) {
            $query->where('mentor_id', $request->mentor_id);
        }
        
        $tasks = $query->get();
        return response()->json(['data' => $tasks], 200);
    }

    public function groupAnalytics($id)
    {
        return response()->json(['message' => 'Analytics for group ' . $id], 200);
    }

    public function exportAnalytics()
    {
        return response()->json(['message' => 'Exporting analytics...'], 200);
    }

    public function updateN8NSettings(Request $request)
    {
        // This is handled in the Laravel Admin Dashboard as per the doc, but we provide a placeholder here
        return response()->json(['message' => 'N8N settings updated'], 200);
    }
}
