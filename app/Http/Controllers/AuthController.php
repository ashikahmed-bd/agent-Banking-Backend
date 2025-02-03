<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'business_id' => 'required|exists:businesses,id',
            'name' => 'required|string|max:255',
            'phone' => 'required|unique:users,phone',
            'email' => 'nullable|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        $customer = User::create([
            'business_id' => $request->business_id,
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'customer',
        ]);

        return response()->json([
            'message' => 'Customer registered successfully',
            'token' => $customer->createToken('API Token')->plainTextToken
        ], 201);
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

        $business = $user->withoutEvents(function () use ($user) {
            return $user->business()->first();
        });

        // Logout if no branch assigned
        if (! $business) {
            return response()->json([
                'success' => false,
                'message' => 'Business not found! Please Contact support team.',
            ], Response::HTTP_UNAUTHORIZED);
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
