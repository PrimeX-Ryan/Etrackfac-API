<?php

namespace App\Http\Controllers;

use App\Models\User;

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
}
