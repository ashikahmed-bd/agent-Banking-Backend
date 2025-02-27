<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\HttpFoundation\Response;

class SettingsController extends Controller
{


    public function closeDay()
    {
        $agentId = Auth::user()->agents()->first()->id ?? null;

        return DB::transaction(function () use ($agentId) {
            // Fetch all accounts of the agent
            $accounts = Account::query()->where('agent_id', $agentId)->get();

            if ($accounts->isEmpty()) {
                throw new Exception("No accounts found for this agent.");
            }

            foreach ($accounts as $account) {
                // Set Closing Balance for the day
                $account->closing_balance = $account->current_balance;
                $account->save();
            }

            return ['success' => true, 'message' => "Day closed successfully!"];
        });
    }

    public function openDay()
    {
        $agentId = Auth::user()->agents()->first()->id ?? null;

        return DB::transaction(function () use ($agentId) {
            // Fetch all accounts of the agent
            $accounts = Account::query()->where('agent_id', $agentId)->get();

            if ($accounts->isEmpty()) {
                throw new Exception("No accounts found for this agent.");
            }

            foreach ($accounts as $account) {
                // Set Opening Balance for the new day based on Closing Balance
                $account->opening_balance = $account->closing_balance;
                $account->save();
            }

            return ['success' => true, 'message' => "Day opened successfully!"];
        });
    }



    public function reboot(): JsonResponse
    {
        artisan::call('optimize');
        artisan::call('optimize:clear');
        artisan::call('config:cache');
        artisan::call('event:cache');
        artisan::call('route:cache');
        artisan::call('view:cache');

        return response()->json([
            'success' => true,
            'message' => 'Reboot Successfully!',
        ], Response::HTTP_OK);
    }

    public function seed(): JsonResponse
    {
        artisan::call('migrate:fresh --seed');

        return response()->json([
            'success' => true,
            'message' => 'Database seeded Successfully!',
        ], Response::HTTP_OK);
    }
}
