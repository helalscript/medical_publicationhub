<?php

namespace App\Http\Controllers\API\V1\N8N;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class N8NController extends Controller
{
    public function getTask($id)
    {
        $task = \App\Models\Task::with('rubric')->findOrFail($id);
        return response()->json(['data' => $task], 200);
    }

    public function getRubric($id)
    {
        $task = \App\Models\Task::findOrFail($id);
        $rubric = \App\Models\Rubric::findOrFail($task->rubric_id);
        return response()->json(['data' => $rubric], 200);
    }

    public function getResearchers($id)
    {
        // Placeholder for fetching researchers by group ID
        // Replace this with actual group logic when available
        $researchers = \App\Models\User::where('role', 'researcher')->get();
        return response()->json(['data' => $researchers], 200);
    }

    public function getVideo($id)
    {
        $task = \App\Models\Task::findOrFail($id);
        return response()->json(['data' => ['video_url' => $task->video_url]], 200);
    }

    public function recordAssignment(Request $request)
    {
        $validated = $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'researcher_ids' => 'required|array',
            'researcher_ids.*' => 'exists:users,id',
        ]);

        $assignments = [];
        foreach ($validated['researcher_ids'] as $researcherId) {
            $assignments[] = \App\Models\TaskAssignment::updateOrCreate(
                ['task_id' => $validated['task_id'], 'researcher_id' => $researcherId],
                ['status' => 'pending']
            );
        }

        return response()->json(['message' => 'Assignments recorded successfully', 'data' => $assignments], 201);
    }

    public function getSubmission($id)
    {
        $submission = \App\Models\Submission::with('taskAssignment.task.rubric')->findOrFail($id);
        
        // Return full file path URL so N8N can download it, or the raw text
        $fileUrl = $submission->file_path ? asset('storage/' . $submission->file_path) : null;
        
        return response()->json([
            'data' => [
                'submission_id' => $submission->id,
                'file_url' => $fileUrl,
                'content_text' => $submission->content_text,
                'rubric' => $submission->taskAssignment->task->rubric
            ]
        ], 200);
    }

    public function saveFeedback(Request $request)
    {
        $validated = $request->validate([
            'submission_id' => 'required|exists:submissions,id',
            'overall_score' => 'required|numeric',
            'rubric_scores' => 'required|array',
            'general_feedback' => 'required|string',
            'flagged_issues' => 'nullable|array',
            'edited_excerpt' => 'nullable|string',
        ]);

        $feedback = \App\Models\AiFeedback::updateOrCreate(
            ['submission_id' => $validated['submission_id']],
            [
                'overall_score' => $validated['overall_score'],
                'rubric_scores' => $validated['rubric_scores'],
                'general_feedback' => $validated['general_feedback'],
                'flagged_issues' => $validated['flagged_issues'] ?? [],
                'edited_excerpt' => $validated['edited_excerpt'] ?? null,
                'status' => 'pending' // Waiting for mentor approval
            ]
        );
        
        $feedback->submission->taskAssignment->update(['status' => 'pending_review']);

        return response()->json(['message' => 'Feedback saved successfully', 'data' => $feedback], 201);
    }
}
