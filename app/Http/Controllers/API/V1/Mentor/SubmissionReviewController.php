<?php

namespace App\Http\Controllers\API\V1\Mentor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SubmissionReviewController extends Controller
{
    public function index()
    {
        // Get all task assignments for tasks created by this mentor that have submissions
        $submissions = \App\Models\Submission::whereHas('taskAssignment.task', function($q) {
            $q->where('mentor_id', auth()->id());
        })->with(['taskAssignment.researcher', 'taskAssignment.task', 'aiFeedback'])->get();

        $data = $submissions->map(function($submission) {
            return [
                'id' => $submission->id,
                'feedback_id' => $submission->aiFeedback?->id,
                'researcher_name' => $submission->taskAssignment?->researcher?->name ?? 'Unknown Researcher',
                'task_name' => $submission->taskAssignment?->task?->title ?? 'Untitled Task',
                'task_title' => $submission->taskAssignment?->task?->title ?? 'Untitled Task',
                'submitted_at' => $submission->submitted_at?->toIso8601String(),
                'status' => $submission->taskAssignment?->status ?? 'pending',
            ];
        });

        return response()->json(['data' => $data], 200);
    }

    public function showFeedback($id)
    {
        $submission = \App\Models\Submission::whereHas('taskAssignment.task', function($q) {
            $q->where('mentor_id', auth()->id());
        })->with('aiFeedback')->findOrFail($id);

        $aiFeedback = $submission->aiFeedback;

        $rubricScores = [];
        if ($aiFeedback && is_array($aiFeedback->rubric_scores)) {
            foreach ($aiFeedback->rubric_scores as $score) {
                $rubricScores[] = [
                    'name' => $score['criterion'] ?? $score['name'] ?? '',
                    'score' => $score['score'] ?? 0,
                    'max' => $score['max_score'] ?? $score['max'] ?? 100,
                ];
            }
        }

        $data = [
            'id' => $submission->id,
            'feedback_id' => $aiFeedback?->id,
            'overall_score' => $aiFeedback?->overall_score ?? 0,
            'mentor_comments' => $aiFeedback?->mentor_comments_override ?? $aiFeedback?->general_feedback,
            'ai_general_feedback' => $aiFeedback?->general_feedback,
            'mentor_score_override' => $aiFeedback?->mentor_score_override,
            'mentor_comments_override' => $aiFeedback?->mentor_comments_override,
            'rubric_scores' => $rubricScores,
            'flagged_issues' => $aiFeedback?->flagged_issues ?? [],
            'ai_excerpt' => $aiFeedback?->edited_excerpt,
            'file_url' => $submission->file_path ? asset('storage/' . $submission->file_path) : null,
            'content_text' => $submission->content_text,
            'ai_overall_score' => $aiFeedback?->overall_score,
        ];

        return response()->json(['data' => $data], 200);
    }

    public function approve(Request $request, $id)
    {
        $feedback = \App\Models\AiFeedback::whereHas('submission.taskAssignment.task', function($q) {
            $q->where('mentor_id', auth()->id());
        })->findOrFail($id);

        $validated = $request->validate([
            'mentor_score_override' => 'nullable|numeric',
            'mentor_comments_override' => 'nullable|string',
        ]);

        $feedback->update([
            'mentor_score_override' => $validated['mentor_score_override'] ?? $feedback->mentor_score_override,
            'mentor_comments_override' => $validated['mentor_comments_override'] ?? $feedback->mentor_comments_override,
            'status' => 'approved',
            'mentor_id' => auth()->id(),
        ]);
        
        $feedback->submission->taskAssignment->update(['status' => 'approved']);

        // Here we would typically trigger an event or job to send chat notification to the researcher

        return response()->json(['message' => 'Feedback approved successfully', 'data' => $feedback], 200);
    }

    public function reject(Request $request, $id)
    {
        $feedback = \App\Models\AiFeedback::whereHas('submission.taskAssignment.task', function($q) {
            $q->where('mentor_id', auth()->id());
        })->findOrFail($id);

        $validated = $request->validate([
            'rejection_note' => 'required|string',
        ]);

        $feedback->update([
            'mentor_comments_override' => $validated['rejection_note'],
            'status' => 'rejected',
            'mentor_id' => auth()->id(),
        ]);
        
        $feedback->submission->taskAssignment->update(['status' => 'rejected']);

        // Here we would typically trigger an event or job to send chat notification to the researcher

        return response()->json(['message' => 'Feedback rejected successfully', 'data' => $feedback], 200);
    }

    public function analytics($groupId)
    {
        // Placeholder for group analytics
        return response()->json(['message' => 'Analytics for group ' . $groupId], 200);
    }
}
