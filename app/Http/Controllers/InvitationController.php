<?php

namespace App\Http\Controllers;

use App\Mail\InvitationMail;
use App\Models\Invitation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
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
}
