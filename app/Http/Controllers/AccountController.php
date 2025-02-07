<?php

namespace App\Http\Controllers;

use App\Enums\PaymentType;
use App\Exceptions\InsufficientBalance;
use App\Http\Resources\AccountResource;
use App\Http\Resources\TransactionResource;
use App\Models\Account;
use App\Models\Customer;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AccountController extends Controller
{
    public function index()
    {
        $accounts = Account::query()->get();
        return AccountResource::collection($accounts);
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string'],
            'number' => ['required', 'string'],
            'opening_balance' => ['required', 'string'],
        ]);

        $account = new Account();
        $account->name = Str::ucfirst($request->name);
        $account->number = $request->number;
        $account->opening_balance = $request->opening_balance;
        $account->logo = Str::snake($request->name).'.svg';
        $account->save();

        return response()->json([
            'success' => true,
            'message' => 'Account Added successful.',
        ], Response::HTTP_CREATED);
    }


    public function getBalance()
    {
        $account = Account::query()->get();

        $total_due = Customer::query()
            ->where('balance', '<', 0) // Customers who owe money
            ->sum('balance');

        $total_payable = Customer::query()
            ->where('balance', '>', 0) // Customers with extra balance
            ->sum('balance');

        $todayCommission = Transaction::query()
            ->whereDate('date', '=' , Carbon::parse(now())->toDateString())
            ->sum('commission');

        $totalCommission = Transaction::query()->sum('commission');

        return response()->json([
            'cash' => $account->where('default', '=', true)->sum('balance'),
            'accounts' => $account->where('default', '=', false)->sum('balance'),
            'wallet' => [
                'due' => $total_due,
                'payable' => $total_payable,
            ],
            'commission' => [
                'today' => $todayCommission,
                'total' => $totalCommission,
            ],
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
        $commission = $request->commission;

        // Atomic transaction for better concurrency
        DB::transaction(function () use ($account_id, $amount, $commission) {
            Transaction::query()->create([
                'account_id' => $account_id,
                'type' => PaymentType::CREDIT,
                'amount' => $amount,
                'commission' => $commission,
                'date' => now(),
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
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Withdrawal successful.',
        ], Response::HTTP_OK);
    }


    public function getTransactions()
    {
        $transactions = Transaction::query()
            ->with(['account'])
            ->latest()
            ->take(10)
            ->get();
        return TransactionResource::collection($transactions);
    }


    public function getHistory(Request $request, string $id)
    {

        $account = Account::query()->findOrFail($id);

        return response()->json([
            'account' => $account,
            'transactions' => $account->transactions()
                ->whereDate('date', '=', Carbon::parse($request->date)
                    ->toDateString())->get(),
        ], Response::HTTP_OK);
    }

}
