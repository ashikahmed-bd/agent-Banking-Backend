<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function register(Request $request) {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        $user = User::query()->create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['token' => $user->createToken('API Token')->plainTextToken]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|exists:users,phone',
            'password' => 'required',
        ]);

        $user = User::query()->where('phone', $request->phone)->firstOrFail();

        // Check if the account is enabled
        if (! $user->active) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], Response::HTTP_FORBIDDEN);
        }

        if (! Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials!',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }


        return response()->json([
            'message' => 'Login successful',
            'token' => $user->createToken('API Token')->plainTextToken,
            'user' => UserResource::make($user)
        ]);
    }

    public function user(Request $request)
    {
        return UserResource::make($request->user());
    }


    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ], Response::HTTP_OK);
    }
}
