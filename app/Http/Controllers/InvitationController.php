<?php

namespace App\Http\Controllers;

use App\Enums\UserType;
use App\Mail\InvitationMail;
use App\Models\Invitation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class InvitationController extends Controller
{
    public function invite(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', Rule::unique('invitations', 'email')],
        ]);

        // Generate an invitation token
        $token = Invitation::generateToken();

        // Create an invitation
        $invitation = Invitation::query()->create([
            'email' => $request->email,
            'token' => $token,
            'invited_by' => auth()->id(),
            'expires_at' => Carbon::now()->addDays(7),
            'company_id' => Auth::user()->companies()->first()->id ?? null,
        ]);

        // Send the invitation email
        Mail::to($request->email)->send(new InvitationMail($invitation));

        return response()->json(['message' => 'Invitation sent successfully']);
    }

    public function acceptInvitation(Request $request)
    {
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

        // Create user
        $user = User::query()->create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $invitation->email,
            'password' => bcrypt($request->password),
            'created_by' => $invitation->invited_by,
            'type' => UserType::MANAGER,
        ]);

        // Mark invitation as accepted
        $invitation->update(['accepted' => true]);

        // Attach user to the company with a role
        if ($invitation->company_id) {
            $user->companies()->attach($invitation->company_id);
        }

        return response()->json(['message' => 'User registered successfully']);
    }
}
