<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\Notification;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        // For Program Chair to see all submissions in their department
        $user = auth()->user();
        $query = Submission::with(['faculty', 'requirement'])
            ->whereHas('faculty', function($q) use ($user) {
                $q->where('department_id', $user->department_id);
            });

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('requirement_id')) {
            $query->where('requirement_id', $request->requirement_id);
        }

        return $query->get();
    }

    public function review(Request $request, Submission $submission)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'remarks' => 'nullable|string'
        ]);

        $submission->update([
            'status' => $request->status,
            'remarks' => $request->remarks
        ]);

        Notification::create([
            'user_id' => $submission->faculty_id,
            'message' => "Your submission for requirement #{$submission->requirement_id} has been {$request->status}."
        ]);

        return response()->json(['success' => true, 'submission' => $submission]);
    }
}
