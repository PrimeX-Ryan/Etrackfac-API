<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function register(Request $request) {
        $messages = [
            'email.unique' => 'This email is already registered.',
            'password.confirmed' => 'The password confirmation does not match.'
        ];

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in(['faculty', 'program_chair', 'dean'])], // Admin is seeded
            'department_id' => 'required|exists:departments,id'
        ], $messages);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'department_id' => $request->department_id,
            'status' => 'pending'
        ]);

        $user->assignRole($request->role);

        // DO NOT Auto login after registration
        // Auth::login($user);

        return response()->json([
            'success' => true,
            'user' => $user->load('roles', 'department'),
            'message' => 'Registration successful! Please wait for admin approval.'
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $user = Auth::user()->load('roles', 'department');
            
            if ($user->status !== 'approved') {
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                return response()->json(['success' => false, 'message' => 'Your account is pending approval.'], 403);
            }

            return response()->json([
                'success' => true,
                'user' => $user
            ]);
        }
        return response()->json(['success' => false, 'message' => 'Invalid credentials'], 401);
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return response()->json(['success' => true]);
    }
}
