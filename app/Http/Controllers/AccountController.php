<?php

namespace App\Http\Controllers;

use App\Enums\PaymentType;
use App\Exceptions\InsufficientBalance;
use App\Http\Resources\AccountResource;
use App\Http\Resources\TransactionResource;
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

        $balance = Account::query()->sum('balance');

        return AccountResource::collection($accounts)->additional([
            'total_balance' => round($balance)
        ]);
    }

    public function getAccounts()
    {
        $accounts = Account::query()
            ->where('default', '=', false)
            ->get();
        return AccountResource::collection($accounts);
    }

    public function getBalance()
    {
        $cash = Account::query()
            ->where('default', '=', true)
            ->sum('balance');

        $wallet = Account::query()
            ->where('default', '=', false)
            ->sum('balance');

        return response()->json([
            'cash' => $cash,
            'wallet' => $wallet,
        ], Response::HTTP_OK);
    }

    public function getCash()
    {
        $balance = Account::query()
            ->where('default', '=', true)
            ->sum('balance');

        return response()->json([
            'balance' => $balance,
        ], Response::HTTP_OK);
    }

    /**
     * @throws InsufficientBalance
     */
    public function deposit(Request $request, $accountId)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);

        $amount = $request->amount;

        // Update cash
        $cash = Account::query()->where('default', '=', true)
            ->firstOrFail();

        if ($cash->getBalance() <= 0){
            throw new InsufficientBalance;
        }
        $cash->decrement('balance', $amount);

        // Atomic transaction for better concurrency
        DB::transaction(function () use ($accountId, $amount) {
            $account = Account::lockForUpdate()->findOrFail($accountId); // Lock row for consistency

            // Update balance
            $account->increment('balance', $amount);

            // Log transaction
            Transaction::query()->create([
                'account_id' => $accountId,
                'type' => PaymentType::CREDIT,
                'amount' => $amount,
                'balance_after_transaction' => $account->balance,
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
            if ($account->getBalance() < $amount) {
                throw new InsufficientBalance;
            }
            // Update balance
            $account->decrement('balance', $amount);

            // Update cash
            $cash = Account::query()->where('default', '=', true)
                ->firstOrFail();
            $cash->increment('balance', $amount);

            // Log transaction
            Transaction::query()->create([
                'account_id' => $accountId,
                'type' => PaymentType::DEBIT,
                'amount' => $amount,
                'balance_after_transaction' => $account->balance,
                'date' => now(),
            ]);
        });

        return response()->json(['message' => 'Withdrawal successful.']);
    }


    public function latestTransaction(Request $request)
    {
        $transactions = Transaction::query()->with(['account'])->latest()->paginate($request->limit);
        return TransactionResource::collection($transactions);
    }
}
