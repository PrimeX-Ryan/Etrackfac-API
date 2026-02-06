<?php

namespace App\Http\Controllers;

use App\Models\Requirement;
use Illuminate\Http\Request;

class RequirementController extends Controller
{
    /**
     * List all requirements with submission counts
     */
    public function index()
    {
        $requirements = Requirement::withCount('submissions')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($requirements);
    }

    /**
     * Create a new requirement
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'semester' => 'required|string|max:100',
            'is_required' => 'boolean'
        ]);

        $requirement = Requirement::create([
            'name' => $request->name,
            'description' => $request->description,
            'semester' => $request->semester,
            'is_required' => $request->is_required ?? true
        ]);

        return response()->json([
            'success' => true,
            'requirement' => $requirement,
            'message' => 'Requirement created successfully'
        ], 201);
    }

    /**
     * Update an existing requirement
     */
    public function update(Request $request, Requirement $requirement)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'semester' => 'required|string|max:100',
            'is_required' => 'boolean'
        ]);

        $requirement->update([
            'name' => $request->name,
            'description' => $request->description,
            'semester' => $request->semester,
            'is_required' => $request->is_required ?? true
        ]);

        return response()->json([
            'success' => true,
            'requirement' => $requirement,
            'message' => 'Requirement updated successfully'
        ]);
    }

    /**
     * Delete a requirement
     */
    public function destroy(Requirement $requirement)
    {
        // Check if there are any submissions for this requirement
        if ($requirement->submissions()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete requirement with existing submissions'
            ], 422);
        }

        $requirement->delete();

        return response()->json([
            'success' => true,
            'message' => 'Requirement deleted successfully'
        ]);
    }
}
