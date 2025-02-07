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
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Symfony\Component\HttpFoundation\Response;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $accounts = Account::query()
            ->where('default', '=', false)
            ->paginate($request->limit);
        return AccountResource::collection($accounts);
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string'],
            'number' => ['required', 'string'],
        ]);


        $account = new Account();
        $account->name = $request->name;
        $account->number = $request->number;

        if ($request->hasFile('logo')){
            $logoUrl = $request->file('logo')->store('accounts', config('app.disk'));
            Image::read($request->file('logo'))->resize(128, 128)->save(Storage::disk(config('app.disk'))->path($logoUrl));
            $account->logo = $logoUrl;
        }
        $account->balance = $request->balance ?? 0;
        $account->active = true;
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

        $todayProfit = Transaction::query()
            ->whereDate('date', '=' , Carbon::parse(now())->toDateString())
            ->sum('profit');

        $totalProfit = Transaction::query()->sum('profit');

        return response()->json([
            'cash' => $account->where('default', '=', true)->sum('balance'),
            'accounts' => $account->where('default', '=', false)->sum('balance'),
            'wallet' => [
                'due' => $total_due,
                'payable' => $total_payable,
            ],
            'profit' => [
                'today' => $todayProfit,
                'total' => $totalProfit,
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
