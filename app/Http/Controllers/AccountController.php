<?php

namespace App\Http\Controllers;

use App\Enums\PaymentType;
use App\Exceptions\InsufficientBalance;
use App\Http\Resources\AccountResource;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\TransactionResource;
use App\Models\Account;
use App\Models\Customer;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
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


    public function getCash()
    {
        $balance = Account::query()
            ->where('business_id', getBusinessId())
            ->where('default', '=', true)
            ->sum('balance');

        return response()->json($balance, Response::HTTP_OK);
    }

    public function getAccountsBalance()
    {
        $account = Account::query()
            ->where('business_id', getBusinessId())
            ->get();

        $wallet = $account->where('default', '=', false)->sum('balance');

        return response()->json($wallet, Response::HTTP_OK);
    }


    public function getWallet()
    {
        $total_due = Customer::query()->where('business_id', getBusinessId())
            ->where('balance', '<', 0) // Customers who owe money
            ->sum('balance');

        $total_payable = Customer::query()->where('business_id', getBusinessId())
            ->where('balance', '>', 0) // Customers with extra balance
            ->sum('balance');

        return response()->json([
            'total_due' => $total_due,  // Convert negative values to positive
            'total_payable' => $total_payable,
        ], Response::HTTP_OK);

    }



    /**
     * @throws InsufficientBalance
     */
    public function deposit(Request $request, string $account_id)
    {

        $request->validate([
            'amount' => 'required|numeric|min:1',
            'note' => 'nullable|string',
        ]);

        $amount = $request->amount;
        $profit = $request->profit;

        // Get default cash account
        $cash = Account::query()->where('default', '=', true)->firstOrFail();

        if ($cash->getBalance() <= 0){
            throw new InsufficientBalance;
        }
        $cash->decrement('balance', $amount);

        // Atomic transaction for better concurrency
        DB::transaction(function () use ($account_id, $amount, $profit) {
            $account = Account::query()->lockForUpdate()->findOrFail($account_id); // Lock row for consistency
            $account->increment('balance', $amount);

            // Log transaction
            Transaction::query()->create([
                'account_id' => $account_id,
                'type' => PaymentType::CREDIT,
                'amount' => $amount,
                'profit' => $profit,
                'balance_after_transaction' => $account->balance,
                'date' => now(),
                'business_id' => $account->business_id,
                'user_id' => Auth::id(),
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Deposit successful.',
        ], Response::HTTP_OK);
    }

    public function withdraw(Request $request, $account_id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'note' => 'nullable|string',
        ]);

        $amount = $request->amount;
        $profit = $request->profit;

        DB::transaction(function () use ($account_id, $amount, $profit) {
            $account = Account::query()->lockForUpdate()->findOrFail($account_id); // Lock row for consistency

            // Check sufficient balance
            if ($account->getBalance() < $amount) {
                throw new InsufficientBalance;
            }
            // Update balance
            $account->decrement('balance', $amount);

            // Update cash
            $cash = Account::query()->where('default', '=', true)->firstOrFail();
            $cash->increment('balance', $amount);

            // Log transaction
            Transaction::query()->create([
                'account_id' => $account_id,
                'type' => PaymentType::DEBIT,
                'amount' => $amount,
                'profit' => $profit,
                'balance_after_transaction' => $account->balance,
                'date' => now(),
                'business_id' => $account->business_id,
                'user_id' => Auth::id(),
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Withdrawal successful.',
        ], Response::HTTP_OK);
    }


    public function latestTransaction()
    {
        $transactions = Transaction::query()->with(['account'])->latest()->take(6)->get();
        return TransactionResource::collection($transactions);
    }

}
