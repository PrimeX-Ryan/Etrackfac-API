<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = User::with(['roles', 'department'])
                     ->orderBy('status', 'desc') // Pending first ('pending' > 'approved') usually, or just custom sort
                     ->orderByRaw("CASE WHEN status = 'pending' THEN 1 ELSE 2 END")
                     ->orderBy('created_at', 'desc')
                     ->get();
        return response()->json($users);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => ['required', Rule::in(['admin', 'dean', 'program_chair', 'faculty'])],
            'department_id' => 'nullable|exists:departments,id'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'department_id' => $request->department_id,
            'status' => 'approved' // Admin created users are auto-approved
        ]);

        $user->assignRole($request->role);

        return response()->json([
            'success' => true,
            'user' => $user->load('roles', 'department'),
            'message' => 'User created successfully'
        ]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::in(['admin', 'dean', 'program_chair', 'faculty'])],
            'department_id' => 'nullable|exists:departments,id'
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->department_id = $request->department_id;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();
        $user->syncRoles([$request->role]);

        return response()->json([
            'success' => true,
            'user' => $user->load('roles', 'department'),
            'message' => 'User updated successfully'
        ]);
    }

    public function approve(User $user)
    {
        $user->update(['status' => 'approved']);
        return response()->json(['success' => true, 'message' => 'User approved successfully']);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(['success' => true, 'message' => 'User rejected/deleted successfully']);
    }

    public function submissions(User $user)
    {
        // Get active semester
        $activeSemester = \App\Models\Semester::where('is_active', true)->first();
        
        if (!$activeSemester) {
            return response()->json([]);
        }

        $requirements = \App\Models\Requirement::where('semester_id', $activeSemester->id)->get();
        $submissions = \App\Models\Submission::where('faculty_id', $user->id)
            ->whereIn('requirement_id', $requirements->pluck('id'))
            ->get()
            ->keyBy('requirement_id');

        $checklist = $requirements->map(function ($r) use ($submissions) {
            return [
                'requirement_id' => $r->id,
                'requirement' => $r->name,
                'status' => $submissions[$r->id]->status ?? 'pending',
                'remarks' => $submissions[$r->id]->remarks ?? null,
                'file_path' => $submissions[$r->id]->file_path ?? null,
                'deadline' => $r->deadline,
                'submission_id' => $submissions[$r->id]->id ?? null,
            ];
        });

        return response()->json($checklist);
    }
}
