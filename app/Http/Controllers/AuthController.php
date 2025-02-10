<?php

namespace App\Http\Controllers;

use App\Enums\UserType;
use App\Http\Resources\UserResource;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function register(Request $request) {

        $request->validate([
            'name' => ['required', 'string'],
            'phone' => ['required', 'string', Rule::unique('users', 'phone')],
            'token' => ['required', 'string', Rule::exists('invitations', 'token')],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $invitation = Invitation::query()->where('token', $request->token)->first();

        if (!$invitation || $invitation->isExpired()) {
            return response()->json(['message' => 'Invalid or expired invitation'], 400);
        }

        $user = User::query()->create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $invitation->email,
            'password' => bcrypt($request->password),
            'created_by' => $invitation->invited_by,
        ]);

        // Mark invitation as accepted
        $invitation->update(['accepted' => true]);

        // Attach the user to the invited company (if exists)
        $user->companies()->attach($invitation->company_id, ['type' => UserType::MANAGER]);

        return response()->json(['message' => 'User registered successfully']);
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
