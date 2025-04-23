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
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (! Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Invalid credentials'], 422);
        }

        $user = Auth::user();
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
}
