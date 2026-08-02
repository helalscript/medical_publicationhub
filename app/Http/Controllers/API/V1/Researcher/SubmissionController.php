<?php

namespace App\Http\Controllers\API\V1\Researcher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'task_assignment_id' => 'required|exists:task_assignments,id',
            'file' => 'nullable|file|mimes:docx,xlsx,pdf|max:10240',
            'content_text' => 'nullable|string',
        ]);

        $assignment = \App\Models\TaskAssignment::where('researcher_id', auth()->id())
                        ->findOrFail($validated['task_assignment_id']);

        $filePath = null;
        $originalFilename = null;
        
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $originalFilename = $file->getClientOriginalName();
            $filePath = $file->store('submissions', 'public');
        }

        $submission = \App\Models\Submission::create([
            'task_assignment_id' => $assignment->id,
            'file_path' => $filePath,
            'original_filename' => $originalFilename,
            'content_text' => $validated['content_text'] ?? null,
            'submitted_at' => now(),
        ]);
        
        $assignment->update(['status' => 'submitted']);

        // Trigger N8N webhook for AI assessment
        $webhookUrl = config('services.n8n.ai_assessment_webhook');
        if ($webhookUrl) {
            try {
                $response = \Illuminate\Support\Facades\Http::post($webhookUrl, [
                    'submission_id' => $submission->id,
                    'student_id'    => $assignment->researcher_id,
                    'task_id'       => $assignment->task_id,
                    'file_path'     => $submission->file_path,
                    'content_text'  => $submission->content_text,
                ]);

                \Illuminate\Support\Facades\Log::channel('stack')->info('[N8N Webhook] Triggered successfully', [
                    'submission_id' => $submission->id,
                    'status_code'   => $response->status(),
                ]);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::channel('stack')->error('[N8N Webhook] Trigger failed', [
                    'submission_id' => $submission->id,
                    'error'         => $e->getMessage(),
                ]);
            }
        } else {
            \Illuminate\Support\Facades\Log::channel('stack')->warning('[N8N Webhook] URL not configured in services config.');
        }

        return response()->json(['message' => 'Task submitted successfully', 'data' => $submission], 201);
    }

    public function feedback($id)
    {
        $submission = \App\Models\Submission::whereHas('taskAssignment', function($q) {
            $q->where('researcher_id', auth()->id());
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
}
