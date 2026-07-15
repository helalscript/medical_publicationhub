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

        return response()->json(['data' => $submissions], 200);
    }

    public function showFeedback($id)
    {
        $submission = \App\Models\Submission::whereHas('taskAssignment.task', function($q) {
            $q->where('mentor_id', auth()->id());
        })->with('aiFeedback')->findOrFail($id);

        return response()->json(['data' => $submission], 200);
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
