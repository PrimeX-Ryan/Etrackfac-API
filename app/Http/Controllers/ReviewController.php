<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\Notification;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index()
    {
        // For Program Chair to see all submissions
        return Submission::with(['faculty', 'requirement'])->get();
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
