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

        // Here we would typically trigger an event or webhook to N8N for AI assessment

        return response()->json(['message' => 'Task submitted successfully', 'data' => $submission], 201);
    }

    public function feedback($id)
    {
        $submission = \App\Models\Submission::whereHas('taskAssignment', function($q) {
            $q->where('researcher_id', auth()->id());
        })->with('aiFeedback')->findOrFail($id);

        return response()->json(['data' => $submission], 200);
    }
}
