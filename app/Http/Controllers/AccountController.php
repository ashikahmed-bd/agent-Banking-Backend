<?php

namespace App\Http\Controllers;

use App\Enums\PaymentType;
use App\Http\Resources\AccountResource;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $accounts = Account::query()->paginate($request->limit);

        $initial = Account::query()->sum('initial_balance');
        $current = Account::query()->sum('current_balance');

        return AccountResource::collection($accounts)->additional([
            'total_balance' => round($initial + $current)
        ]);
    }

    public function getBalance()
    {
        $initial = Account::query()->sum('initial_balance');
        $current = Account::query()->sum('current_balance');

        return response()->json([
            'balance' => round($initial + $current),
        ], Response::HTTP_OK);
    }

    public function deposit(Request $request, $accountId)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);

        $amount = $request->amount;

        // Atomic transaction for better concurrency
        DB::transaction(function () use ($accountId, $amount) {
            $account = Account::lockForUpdate()->findOrFail($accountId); // Lock row for consistency

            // Update balance
            $account->increment('current_balance', $amount);

            // Log transaction
            Transaction::query()->create([
                'account_id' => $accountId,
                'type' => PaymentType::CREDIT,
                'amount' => $amount,
                'balance_after_transaction' => $account->current_balance,
                'date' => now(),
            ]);
        });

        return response()->json(['message' => 'Deposit successful.']);
    }

    public function withdraw(Request $request, $accountId)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);

        $amount = $request->amount;

        DB::transaction(function () use ($accountId, $amount) {
            $account = Account::lockForUpdate()->findOrFail($accountId); // Lock row for consistency

            // Check sufficient balance
            if ($account->current_balance < $amount) {
                throw new \Exception('Insufficient balance.');
            }

            // Update balance
            $account->decrement('current_balance', $amount);

            // Log transaction
            Transaction::create([
                'account_id' => $accountId,
                'type' => 'withdraw',
                'amount' => $amount,
                'balance_after_transaction' => $account->current_balance,
            ]);
        });

        return response()->json(['message' => 'Withdrawal successful.']);
    }


    public function balanceSheet()
    {
        $accounts = Account::all();
        $date = Carbon::yesterday(); // Generate for the previous day

        foreach ($accounts as $account) {
            $openingBalance = BalanceSheet::where('account_id', $account->id)
                ->where('date', '<', $date)
                ->orderBy('date', 'desc')
                ->value('closing_balance') ?? $account->initial_balance;

            $transactions = Transaction::query()->where('account_id', $account->id)
                ->whereDate('date', $date)
                ->get();

            $credits = $transactions->where('type', 'credit')->sum('amount');
            $debits = $transactions->where('type', 'debit')->sum('amount');
            $closingBalance = $openingBalance + $credits - $debits;

            BalanceSheet::updateOrCreate(
                ['account_id' => $account->id, 'date' => $date],
                [
                    'opening_balance' => $openingBalance,
                    'closing_balance' => $closingBalance,
                    'credits_total' => $credits,
                    'debits_total' => $debits,
                ]
            );
        }
    }
}
