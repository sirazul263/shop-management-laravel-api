<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Register a new user
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),

        ]);

        return response()->json([
            'status' => 1,
            'message' => 'User registered successfully',
            'data' => $user,
        ], 200);
    }

    // Log in a user and generate a token
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (! Auth::attempt($validated)) {
            return response()->json([
                'status' => 0,
                'message' => 'Invalid credentials',
            ], 422);
        }

        $user = Auth::user();

        if ($user->status === 'INACTIVE') {
            Auth::logout(); // Logout immediately if status is INACTIVE

            return response()->json([
                'status' => 0,
                'message' => 'Your account is inactive. Please contact support.',
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 1,
            'message' => 'Login successful',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }

    // Get authenticated user info
    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    // Log out the user and revoke token
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function getUsers()
    {
        try {
            $users = User::with('stores')->get();

            return response()->json([
                'status' => 1,
                'message' => 'Users retrieved successfully',
                'data' => $users],
                200);
        } catch (Exception $e) {

            return response()->json([
                'status' => 0,
                'message' => 'Failed to retrieve users'],
                500);
        }
    }

    public function createUser(UserRequest $request)
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
            ]);

            // Attach the store_id to the user via the pivot table
            $user->stores()->attach($request->store_id);

            return response()->json([
                'status' => 1,
                'message' => 'Users created successfully',
                'data' => $user,
            ],
                200);
        } catch (Exception $e) {

            return response()->json([
                'status' => 0,
                'message' => 'Failed to create user'],
                500);
        }
    }

    public function updateUser($userId, Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'status' => ['required', 'in:ACTIVE,INACTIVE'],
            ]);

            $user = User::find($userId);
            if (! $user) {
                return response()->json([
                    'status' => 0,
                    'message' => 'User not found',
                ]);
            }
            $user->status = $validated['status'];
            $user->name = $validated['name'];
            $user->save();

            return response()->json([
                'status' => 1,
                'message' => 'User updated successfully.',
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Failed to update user'],
                500);
        }
    }

    public function statusUpdate(Request $request)
    {
        try {

            $validated = $request->validate([
                'user_id' => ['required', 'integer', 'exists:users,id'],
                'status' => ['required', 'in:ACTIVE,INACTIVE'],
            ]);

            $user = User::find($validated['user_id']);

            if (! $user) {
                return response()->json([
                    'status' => 0,
                    'message' => 'User not found',
                ]);
            }

            $user->status = $validated['status'];
            $user->save();

            return response()->json([
                'status' => 1,
                'message' => 'Status changed successfully.',
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Failed to change status'],
                500);
        }
    }

    public function changePassword(Request $request)
    {
        try {
            $request->validate([
                'current_password' => ['required', 'string', 'min:8'],
                'new_password' => ['required', 'string', 'min:8', 'different:current_password', 'confirmed'],
            ]);

            $user = Auth::user();

            if (! Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Current password is incorrect.'],
                    422);
            }

            $user->password = Hash::make($request->new_password);
            $user->save();

            return response()->json([
                'status' => 1,
                'message' => 'Password changed successfully.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Failed to change password'],
                500);
        }

    }
}
