<?php

namespace App\Http\Controllers\API\V1\Mentor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = \App\Models\Task::where('mentor_id', auth()->id())->with('rubric')->get();
        return response()->json(['data' => $tasks], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'rubric_id' => 'required|exists:rubrics,id',
            'deadline' => 'required|date',
            'video_url' => 'nullable|url',
        ]);

        $task = \App\Models\Task::create([
            'mentor_id' => auth()->id(),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'rubric_id' => $validated['rubric_id'],
            'deadline' => \Carbon\Carbon::parse($validated['deadline']),
            'video_url' => $validated['video_url'] ?? null,
        ]);

        return response()->json(['message' => 'Task created successfully', 'data' => $task], 201);
    }

    public function show($id)
    {
        $task = \App\Models\Task::where('mentor_id', auth()->id())->with('rubric')->findOrFail($id);
        return response()->json(['data' => $task], 200);
    }

    public function update(Request $request, $id)
    {
        $task = \App\Models\Task::where('mentor_id', auth()->id())->findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'rubric_id' => 'sometimes|required|exists:rubrics,id',
            'deadline' => 'sometimes|required|date',
            'video_url' => 'nullable|url',
        ]);

        if(isset($validated['deadline'])) {
            $validated['deadline'] = \Carbon\Carbon::parse($validated['deadline']);
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
}
