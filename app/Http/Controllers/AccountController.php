<?php

namespace App\Http\Controllers;

use App\Enums\PaymentType;
use App\Http\Resources\AccountResource;
use App\Http\Resources\TransactionResource;
use App\Models\Account;
use App\Models\Customer;
use App\Models\Transaction;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            'balance' => ['required', 'string'],
        ]);

        $account = new Account();
        $account->name = Str::ucfirst($request->name);
        $account->number = $request->number;
        $account->balance = $request->balance;
        $account->logo = Str::snake($request->name).'.svg';
        $account->company_id = Auth::user()->companies()->first()->id ?? null;
        $account->save();

        return response()->json([
            'success' => true,
            'message' => 'Account Added successful.',
        ], Response::HTTP_CREATED);
    }


    /**
     * @throws Exception
     */
    public function deposit(Request $request, string $account): \Illuminate\Http\JsonResponse
    {
        $account = Account::query()->findOrFail($account);
        $account->deposit($request->amount);

        // Log transaction
        Transaction::query()->create([
            'account_id' => $account->id,
            'type' => PaymentType::CREDIT,
            'amount' => $request->amount,
            'commission' => $request->commission,
            'net_amount' => ($request->amount + $request->commission),
            'description' => 'Cash to Bank transfer'
        ]);

        return response()->json([
            'message' => 'Transfer successful',
            'new_balance' => $account,
        ], 200);
    }

    /**
     * @throws Exception
     */
    public function withdraw(Request $request, string $account): \Illuminate\Http\JsonResponse
    {
        $account = Account::query()->findOrFail($account);
        $account->withdraw($request->amount);

        // Log transaction
        Transaction::query()->create([
            'account_id' => $account->id,
            'type' => PaymentType::DEBIT,
            'amount' => $request->amount,
            'commission' => $request->commission,
            'net_amount' => ($request->amount + $request->commission),
            'description' => 'Cash to Bank transfer'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Withdraw successful.',
            'current_balance' => $account->balance,
        ], Response::HTTP_OK);
    }


    public function getBalance()
    {
        // Get all accounts
        $accounts = Account::all();

        // Sum the 'opening_balance' from all accounts
        $total = $accounts->sum('opening_balance');

        // Sum all CREDIT transactions for all accounts
        $creditTotal = Transaction::query()->whereIn('account_id', $accounts->pluck('id'))
            ->where('type', PaymentType::CREDIT->value)
            ->sum('amount');

        $debitTotal = Transaction::query()->whereIn('account_id', $accounts->pluck('id'))
            ->where('type', PaymentType::DEBIT->value)
            ->sum('amount');

        // Add credit transactions to total balance
        return ($total + $creditTotal) - $debitTotal;
    }

    public function getTransactions(Request $request, string $id)
    {
        $account = Account::query()->findOrFail($id);
        $transactions = $account->transactions()
            ->with(['user'])
            ->whereDate('created_at', '=', Carbon::parse($request->date)
                ->toDateString())->get();

        return TransactionResource::collection($transactions);
    }


    public function getLatestTransactions()
    {
        $transactions = Transaction::query()->with(['user' , 'account'])
            ->whereDate('created_at', '=', Carbon::parse(now())
                ->toDateString())->get();
        return TransactionResource::collection($transactions);
    }


    public function getAllBalances()
    {

        $cash = Account::query()->get();

        $due = Customer::query()
            ->where('balance', '<', 0) // Customers who owe money
            ->sum('balance');

        $payable = Customer::query()
            ->where('balance', '>', 0) // Customers with extra balance
            ->sum('balance');

        return [
            'cash' => $cash->where('default', '=', true)->sum('balance'),
            'accounts' => $cash->where('default', '=', false)->sum('balance'),
            'wallet' => [
                'due' => $due,
                'payable' => $payable,
            ]
        ];
    }

}
