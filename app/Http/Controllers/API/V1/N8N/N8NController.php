<?php

namespace App\Http\Controllers\API\V1\N8N;

use App\Http\Controllers\Controller;
use App\Models\AiFeedback;
use App\Models\Rubric;
use App\Models\Submission;
use App\Models\Task;
use App\Models\TaskAssignment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;


class N8NController extends Controller
{
    // ─────────────────────────────────────────────────────────────
    // GET /api/n8n/tasks/{id}
    // ─────────────────────────────────────────────────────────────
    public function getTask($id)
    {
        Log::channel('stack')->info('[N8N] getTask called', ['task_id' => $id]);

        try {
            $task = Task::with('rubric')->findOrFail($id);

            Log::channel('stack')->info('[N8N] getTask success', [
                'task_id'   => $task->id,
                'title'     => $task->title,
                'rubric_id' => $task->rubric_id,
            ]);

            return response()->json(['data' => $task], 200);

        } catch (\Exception $e) {
            Log::channel('stack')->error('[N8N] getTask failed', [
                'task_id' => $id,
                'error'   => $e->getMessage(),
            ]);
            return response()->json(['error' => 'Task not found', 'task_id' => $id], 404);
        }
    }

    // ─────────────────────────────────────────────────────────────
    // GET /api/n8n/tasks/{id}/rubric
    // ─────────────────────────────────────────────────────────────
    public function getRubric($id)
    {
        Log::channel('stack')->info('[N8N] getRubric called', ['task_id' => $id]);

        try {
            $task   = Task::findOrFail($id);
            $rubric = Rubric::findOrFail($task->rubric_id);

            Log::channel('stack')->info('[N8N] getRubric success', [
                'task_id'   => $task->id,
                'rubric_id' => $rubric->id,
                'title'     => $rubric->title,
                'criteria_count' => count($rubric->criteria ?? []),
            ]);

            return response()->json(['data' => $rubric], 200);

        } catch (\Exception $e) {
            Log::channel('stack')->error('[N8N] getRubric failed', [
                'task_id' => $id,
                'error'   => $e->getMessage(),
            ]);
            return response()->json(['error' => 'Rubric not found for task', 'task_id' => $id], 404);
        }
    }

    // ─────────────────────────────────────────────────────────────
    // GET /api/n8n/groups/{id}/researchers
    // ─────────────────────────────────────────────────────────────
    public function getResearchers($id)
    {
        Log::channel('stack')->info('[N8N] getResearchers called', ['group_id' => $id]);

        try {
            $researchers = User::where('role', 'researcher')->get();

            Log::channel('stack')->info('[N8N] getResearchers success', [
                'group_id' => $id,
                'count'    => $researchers->count(),
            ]);

            return response()->json(['data' => $researchers], 200);

        } catch (\Exception $e) {
            Log::channel('stack')->error('[N8N] getResearchers failed', [
                'group_id' => $id,
                'error'    => $e->getMessage(),
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────
    // GET /api/n8n/videos/{id}
    // ─────────────────────────────────────────────────────────────
    public function getVideo($id)
    {
        Log::channel('stack')->info('[N8N] getVideo called', ['task_id' => $id]);

        try {
            $task = Task::findOrFail($id);

            Log::channel('stack')->info('[N8N] getVideo success', [
                'task_id'   => $task->id,
                'video_url' => $task->video_url,
            ]);

            return response()->json(['data' => ['video_url' => $task->video_url]], 200);

        } catch (\Exception $e) {
            Log::channel('stack')->error('[N8N] getVideo failed', [
                'task_id' => $id,
                'error'   => $e->getMessage(),
            ]);
            return response()->json(['error' => 'Task not found', 'task_id' => $id], 404);
        }
    }

    // ─────────────────────────────────────────────────────────────
    // POST /api/n8n/task-assignments
    // ─────────────────────────────────────────────────────────────
    public function recordAssignment(Request $request)
    {
        Log::channel('stack')->info('[N8N] recordAssignment called', [
            'payload' => $request->all(),
        ]);

        try {
            $validated = $request->validate([
                'task_id'          => 'required|exists:tasks,id',
                'researcher_ids'   => 'required|array',
                'researcher_ids.*' => 'exists:users,id',
            ]);

            $assignments = [];
            foreach ($validated['researcher_ids'] as $researcherId) {
                $assignments[] = TaskAssignment::updateOrCreate(
                    ['task_id' => $validated['task_id'], 'researcher_id' => $researcherId],
                    ['status'  => 'pending']
                );
            }

            Log::channel('stack')->info('[N8N] recordAssignment success', [
                'task_id'          => $validated['task_id'],
                'assignments_count'=> count($assignments),
            ]);

            return response()->json([
                'message' => 'Assignments recorded successfully',
                'data'    => $assignments,
            ], 201);

        } catch (ValidationException $e) {
            Log::channel('stack')->warning('[N8N] recordAssignment validation failed', [
                'errors'  => $e->errors(),
                'payload' => $request->all(),
            ]);
            return response()->json(['error' => 'Validation failed', 'details' => $e->errors()], 422);

        } catch (\Exception $e) {
            Log::channel('stack')->error('[N8N] recordAssignment failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────
    // GET /api/n8n/submissions/{id}
    // ─────────────────────────────────────────────────────────────
    public function getSubmission($id)
    {
        Log::channel('stack')->info('[N8N] getSubmission called', ['submission_id' => $id]);

        try {
            $submission = Submission::with('taskAssignment.task.rubric')->findOrFail($id);
            $fileUrl    = $submission->file_path
                ? asset('storage/' . $submission->file_path)
                : null;

            $data = [
                'submission_id' => $submission->id,
                'file_url'      => $fileUrl,
                'content_text'  => $submission->content_text,
                'rubric'        => $submission->taskAssignment->task->rubric ?? null,
            ];

            Log::channel('stack')->info('[N8N] getSubmission success', [
                'submission_id'    => $submission->id,
                'has_file'         => !is_null($fileUrl),
                'has_content_text' => !is_null($submission->content_text),
                'has_rubric'       => !is_null($data['rubric']),
            ]);

            return response()->json(['data' => $data], 200);

        } catch (\Exception $e) {
            Log::channel('stack')->error('[N8N] getSubmission failed', [
                'submission_id' => $id,
                'error'         => $e->getMessage(),
            ]);
            return response()->json(['error' => 'Submission not found', 'submission_id' => $id], 404);
        }
    }

    // ─────────────────────────────────────────────────────────────
    // POST /api/n8n/ai-feedback
    // Accepts multiple AI response formats:
    //   1. Direct JSON fields (structured)
    //   2. message.content string (GPT Chat model)
    //   3. text string (GPT Completion model)
    //   4. ai_response wrapper object
    // ─────────────────────────────────────────────────────────────
    public function saveFeedback(Request $request)
    {
        // ── Step 1: Log everything received ──────────────────────
        Log::channel('stack')->info('[N8N] saveFeedback called — raw payload received', [
            'all_input'    => $request->all(),
            'content_type' => $request->header('Content-Type'),
        ]);

        // ── Step 2: Normalize AI response into a flat array ──────
        $parsed = $this->parseAiResponseFromRequest($request);

        Log::channel('stack')->info('[N8N] saveFeedback — parsed AI data', [
            'submission_id'    => $parsed['submission_id'] ?? null,
            'overall_score'    => $parsed['overall_score'] ?? null,
            'has_rubric_scores'=> !empty($parsed['rubric_scores']),
            'has_general_feedback' => !empty($parsed['general_feedback']),
            'parse_source'     => $parsed['_parse_source'] ?? 'unknown',
        ]);

        // ── Step 3: Validate the normalized data ─────────────────
        try {
            $validator = \Illuminate\Support\Facades\Validator::make($parsed, [
                'submission_id'    => 'required|exists:submissions,id',
                'overall_score'    => 'required|numeric|min:0|max:100',
                'rubric_scores'    => 'required|array',
                'general_feedback' => 'required|string',
                'flagged_issues'   => 'nullable|array',
                'edited_excerpt'   => 'nullable|string',
            ]);

            if ($validator->fails()) {
                Log::channel('stack')->error('[N8N] saveFeedback VALIDATION ERROR', [
                    'errors'        => $validator->errors()->toArray(),
                    'parsed_data'   => $parsed,
                    'raw_input'     => $request->all(),
                    'hint'          => 'Check OpenAI node response format in n8n. overall_score, rubric_scores, general_feedback are required.',
                ]);

                return response()->json([
                    'error'       => 'Validation failed — AI response fields missing or wrong format',
                    'details'     => $validator->errors(),
                    'parsed_data' => $parsed,
                    'hint'        => 'Ensure OpenAI returns JSON with: overall_score (number), rubric_scores (array), general_feedback (string)',
                ], 422);
            }

            $validated = $validator->validated();

            // ── Step 4: Save feedback ─────────────────────────────
            $feedback = AiFeedback::updateOrCreate(
                ['submission_id' => $validated['submission_id']],
                [
                    'overall_score'    => $validated['overall_score'],
                    'rubric_scores'    => $validated['rubric_scores'],
                    'general_feedback' => $validated['general_feedback'],
                    'flagged_issues'   => $validated['flagged_issues'] ?? [],
                    'edited_excerpt'   => $validated['edited_excerpt'] ?? null,
                    'status'           => 'pending', // Waiting for mentor approval
                ]
            );

            // Update assignment status
            $feedback->submission->taskAssignment->update(['status' => 'pending_review']);

            Log::channel('stack')->info('[N8N] saveFeedback SUCCESS — AI feedback saved to DB', [
                'feedback_id'   => $feedback->id,
                'submission_id' => $feedback->submission_id,
                'overall_score' => $feedback->overall_score,
                'status'        => $feedback->status,
                'parse_source'  => $parsed['_parse_source'] ?? 'unknown',
            ]);

            return response()->json([
                'message' => 'Feedback saved successfully',
                'data'    => $feedback,
            ], 201);

        } catch (\Exception $e) {
            Log::channel('stack')->error('[N8N] saveFeedback EXCEPTION', [
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'payload' => $request->all(),
            ]);

            return response()->json([
                'error'   => 'Internal server error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────
    // PRIVATE: Parse AI response from multiple possible formats
    // ─────────────────────────────────────────────────────────────
    private function parseAiResponseFromRequest(Request $request): array
    {
        $base = [
            'submission_id' => $request->input('submission_id'),
            '_parse_source' => 'unknown',
        ];

        // ── Format 1: Fields sent directly (already structured) ──
        if ($request->has('overall_score') && !is_null($request->input('overall_score'))) {
            Log::channel('stack')->info('[N8N] parseAiResponse: Format 1 — direct fields');
            return array_merge($base, [
                'overall_score'    => $request->input('overall_score'),
                'rubric_scores'    => $request->input('rubric_scores', []),
                'general_feedback' => $request->input('general_feedback', ''),
                'flagged_issues'   => $request->input('flagged_issues', []),
                'edited_excerpt'   => $request->input('edited_excerpt'),
                '_parse_source'    => 'direct_fields',
            ]);
        }

        // ── Format 2: ai_response wrapper key ────────────────────
        $aiResponse = $request->input('ai_response');
        if ($aiResponse) {
            $decoded = is_string($aiResponse) ? json_decode($aiResponse, true) : $aiResponse;
            if (is_array($decoded) && isset($decoded['overall_score'])) {
                Log::channel('stack')->info('[N8N] parseAiResponse: Format 2 — ai_response wrapper');
                return array_merge($base, $decoded, ['_parse_source' => 'ai_response_wrapper']);
            }
        }

        // ── Format 3: GPT Chat model → message.content (string) ──
        $messageContent = $request->input('message.content')
            ?? $request->input('message')['content']
            ?? null;

        if ($messageContent && is_string($messageContent)) {
            $decoded = $this->extractJsonFromString($messageContent);
            if ($decoded && isset($decoded['overall_score'])) {
                Log::channel('stack')->info('[N8N] parseAiResponse: Format 3 — GPT Chat message.content');
                return array_merge($base, $decoded, ['_parse_source' => 'gpt_chat_message_content']);
            }
        }

        // ── Format 4: GPT Completion model → text field ──────────
        $textContent = $request->input('text')
            ?? $request->input('choices.0.text')
            ?? null;

        if ($textContent && is_string($textContent)) {
            $decoded = $this->extractJsonFromString($textContent);
            if ($decoded && isset($decoded['overall_score'])) {
                Log::channel('stack')->info('[N8N] parseAiResponse: Format 4 — GPT Completion text field');
                return array_merge($base, $decoded, ['_parse_source' => 'gpt_completion_text']);
            }

            // Text exists but no JSON found — log raw text
            Log::channel('stack')->warning('[N8N] parseAiResponse: text field found but no JSON extracted', [
                'text_preview' => substr($textContent, 0, 300),
            ]);
        }

        // ── Format 5: Entire body is JSON string ─────────────────
        $rawBody = $request->getContent();
        if ($rawBody) {
            $decoded = $this->extractJsonFromString($rawBody);
            if ($decoded && isset($decoded['overall_score'])) {
                $merged = array_merge($base, $decoded, ['_parse_source' => 'raw_body_json']);
                if (isset($decoded['submission_id'])) {
                    $merged['submission_id'] = $decoded['submission_id'];
                }
                Log::channel('stack')->info('[N8N] parseAiResponse: Format 5 — raw body JSON');
                return $merged;
            }
        }

        Log::channel('stack')->warning('[N8N] parseAiResponse: No recognized format found', [
            'all_keys' => array_keys($request->all()),
            'raw_body_preview' => substr($rawBody ?? '', 0, 300),
        ]);

        return array_merge($base, ['_parse_source' => 'none_matched']);
    }

    // ─────────────────────────────────────────────────────────────
    // PRIVATE: Extract JSON object from a string (handles markdown code blocks)
    // ─────────────────────────────────────────────────────────────
    private function extractJsonFromString(string $text): ?array
    {
        // Remove markdown code fences if present: ```json ... ```
        $text = preg_replace('/```(?:json)?\s*([\s\S]*?)\s*```/', '$1', $text);

        // Try direct decode first
        $decoded = json_decode(trim($text), true);
        if (is_array($decoded)) {
            return $decoded;
        }

        // Try to find a JSON object within the string
        if (preg_match('/\{[\s\S]*\}/s', $text, $matches)) {
            $decoded = json_decode($matches[0], true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return null;
    }
}
