<?php

namespace App\Http\Controllers;


use App\Http\Resources\UserResource;
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
            'email' => ['required', 'string', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:6'],
        ]);

        User::query()->create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

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
                'message' => trans('auth.disabled'),
            ], Response::HTTP_FORBIDDEN);
        }

        if (! Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => trans('auth.failed'),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Retrieve the first company without triggering model events
        $company = $user->withoutEvents(fn () => $user->companies()->first());

        // Determine redirection URL
        $redirectUrl = $company
            ? config('app.client_url') // Redirect to dashboard if company exists
            : config('app.client_url') . '/companies/create'; // Redirect to company creation if none

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'token' => $user->createToken('API Token')->plainTextToken,
            'user' => new UserResource($user),
            'redirect_url' => $redirectUrl,
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
