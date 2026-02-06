<?php

namespace App\Http\Controllers;

use App\Models\Requirement;
use App\Models\Submission;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubmissionController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'requirement_id' => 'required|exists:requirements,id',
            'document' => 'required|file|mimes:pdf,docx,doc'
        ]);

        $path = $request->file('document')->store('submissions', 'public');

        $submission = Submission::updateOrCreate(
            [
                'faculty_id' => Auth::id(),
                'requirement_id' => $request->requirement_id,
            ],
            [
                'file_path' => $path,
                'status' => 'pending',
                'remarks' => null // Clear remarks on re-upload
            ]
        );

        Notification::create([
            'user_id' => Auth::id(),
            'message' => "Your submission for requirement #{$request->requirement_id} has been uploaded."
        ]);

        return response()->json(['success' => true, 'submission' => $submission]);
    }

    public function checklist()
    {
        $requirements = Requirement::all();
        $submissions = Submission::where('faculty_id', Auth::id())->get()->keyBy('requirement_id');

        $checklist = $requirements->map(function ($r) use ($submissions) {
            return [
                'requirement_id' => $r->id,
                'requirement' => $r->name,
                'status' => $submissions[$r->id]->status ?? 'pending',
                'remarks' => $submissions[$r->id]->remarks ?? null,
                'file_path' => $submissions[$r->id]->file_path ?? null,
            ];
        });

        return response()->json($checklist);
    }
}
