<?php

namespace App\Http\Controllers;

use App\Enums\UserType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string'],
            'phone' => ['required', 'string', Rule::unique('users', 'phone')],
            'email' => ['required', 'string', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:6'],
        ]);


        $owner = Auth::user();

        if (!$owner->companies()->exists()) {
            return response()->json([
                'success' => false,
                'error' => trans('messages.no_company'),
            ], Response::HTTP_FORBIDDEN);
        }

        $user = User::query()->create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'type' => UserType::MANAGER,
            'active' => true,
            'created_by' => Auth::id(),
        ]);

        $owner->companies()->first()->users()->attach($user->id);

        return response()->json([
            'success' => true,
            'message' => 'User added successfully',
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
