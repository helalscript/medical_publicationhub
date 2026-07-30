<?php

namespace App\Http\Controllers\API\V1\Mentor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = \App\Models\Task::where('mentor_id', auth()->id())->with(['rubric', 'opportunity', 'assignments'])->get();
        return response()->json(['data' => $tasks], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'rubric_id' => 'nullable|exists:rubrics,id',
            'deadline' => 'required|date',
            'video_url' => 'nullable|url',
            'opportunity_id' => 'required|exists:opportunities,id',
            'assign_type' => 'required|in:all,specific',
            'researcher_ids' => 'required_if:assign_type,specific|array',
            'researcher_ids.*' => 'exists:users,id',
            'document' => 'nullable|file|max:10240',
        ]);

        $documentPath = null;
        $documentName = null;
        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $documentName = $file->getClientOriginalName();
            $documentPath = \App\Helpers\Helper::fileUpload($file, 'tasks', 'task-doc-' . time());
        }

        $task = \App\Models\Task::create([
            'mentor_id' => auth()->id(),
            'opportunity_id' => $validated['opportunity_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'rubric_id' => $validated['rubric_id'] ?? null,
            'deadline' => \Carbon\Carbon::parse($validated['deadline']),
            'video_url' => $validated['video_url'] ?? null,
            'document_path' => $documentPath,
            'document_name' => $documentName,
        ]);

        // Dispatch background queue job for assignment and notification/events
        \App\Jobs\AssignTaskToResearchersJob::dispatch(
            $task->id,
            $validated['opportunity_id'],
            $validated['assign_type'],
            $validated['researcher_ids'] ?? []
        );

        return response()->json(['message' => 'Task created successfully and assignments are processing', 'data' => $task], 201);
    }

    public function show($id)
    {
        $task = \App\Models\Task::where('mentor_id', auth()->id())
            ->with(['rubric', 'opportunity', 'assignments.researcher', 'assignments.submissions.aiFeedback'])
            ->findOrFail($id);
        return response()->json(['data' => $task], 200);
    }

    public function update(Request $request, $id)
    {
        $task = \App\Models\Task::where('mentor_id', auth()->id())->findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'rubric_id' => 'sometimes|nullable|exists:rubrics,id',
            'deadline' => 'sometimes|required|date',
            'video_url' => 'nullable|url',
            'document' => 'nullable|file|max:10240',
        ]);

        if(isset($validated['deadline'])) {
            $validated['deadline'] = \Carbon\Carbon::parse($validated['deadline']);
        }

        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $validated['document_name'] = $file->getClientOriginalName();
            $validated['document_path'] = \App\Helpers\Helper::fileUpload($file, 'tasks', 'task-doc-' . time());
        }

        $task->update($validated);

        return response()->json(['message' => 'Task updated successfully', 'data' => $task], 200);
    }

    public function destroy($id)
    {
        $task = \App\Models\Task::where('mentor_id', auth()->id())->findOrFail($id);
        $task->delete();

        return response()->json(['message' => 'Task deleted successfully'], 200);
    }

    public function assignResearchers(Request $request, $id)
    {
        $task = \App\Models\Task::where('mentor_id', auth()->id())->findOrFail($id);

        $validated = $request->validate([
            'researcher_ids' => 'required|array|min:1',
            'researcher_ids.*' => 'required|exists:users,id',
        ]);

        $assignedCount = 0;
        foreach ($validated['researcher_ids'] as $userId) {
            $assignment = \App\Models\TaskAssignment::updateOrCreate(
                ['task_id' => $task->id, 'researcher_id' => $userId],
                ['status' => 'pending']
            );

            $assignedCount++;
            $user = \App\Models\User::find($userId);
            if ($user) {
                $user->notify(new \App\Notifications\TaskAssignedNotification($task));
                event(new \App\Events\TaskAssignedEvent($task, $userId));
            }
        }

        return response()->json([
            'message' => 'Researchers assigned successfully',
            'assigned_count' => $assignedCount
        ], 200);
    }
}
