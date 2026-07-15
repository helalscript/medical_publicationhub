<?php

namespace App\Http\Controllers\API\V1\Researcher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TaskAssignmentController extends Controller
{
    public function index()
    {
        // Get all task assignments for the authenticated researcher
        $tasks = \App\Models\TaskAssignment::where('researcher_id', auth()->id())
            ->with(['task.rubric'])
            ->get();
            
        return response()->json(['data' => $tasks], 200);
    }
}
