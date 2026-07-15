<?php

namespace App\Http\Controllers\API\V1\Mentor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RubricController extends Controller
{
    public function index()
    {
        $mentorId = auth()->id();
        $rubrics = \App\Models\Rubric::where('mentor_id', $mentorId)
            ->orWhere('is_global', true)
            ->get();
            
        return response()->json(['data' => $rubrics], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'criteria' => 'required|array',
            'criteria.*.name' => 'required|string',
            'criteria.*.points' => 'required|numeric',
        ]);

        $rubric = \App\Models\Rubric::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'is_global' => false,
            'mentor_id' => auth()->id(),
            'criteria' => $validated['criteria'],
        ]);

        return response()->json(['message' => 'Rubric created successfully', 'data' => $rubric], 201);
    }

    public function show($id)
    {
        $mentorId = auth()->id();
        $rubric = \App\Models\Rubric::where(function($query) use ($mentorId) {
            $query->where('mentor_id', $mentorId)
                  ->orWhere('is_global', true);
        })->findOrFail($id);

        return response()->json(['data' => $rubric], 200);
    }

    public function update(Request $request, $id)
    {
        $rubric = \App\Models\Rubric::where('mentor_id', auth()->id())->findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'criteria' => 'sometimes|required|array',
            'criteria.*.name' => 'required|string',
            'criteria.*.points' => 'required|numeric',
        ]);

        $rubric->update($validated);

        return response()->json(['message' => 'Rubric updated successfully', 'data' => $rubric], 200);
    }

    public function destroy($id)
    {
        $rubric = \App\Models\Rubric::where('mentor_id', auth()->id())->findOrFail($id);
        $rubric->delete();

        return response()->json(['message' => 'Rubric deleted successfully'], 200);
    }
}
