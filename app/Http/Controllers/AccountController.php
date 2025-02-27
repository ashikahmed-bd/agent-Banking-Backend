<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Http\Resources\AccountResource;
use App\Http\Resources\TransactionResource;
use App\Models\Account;
use App\Models\Agent;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $accounts = Account::query()->get();
        return AccountResource::collection($accounts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'number' => 'required|unique:accounts,number',
            'opening_balance' => 'required|numeric|min:0',
        ]);

        $agent = Agent::query()->where('created_by', auth()->id())->first();

        if (!$agent) {
            return response()->json([
                'success' => false,
                'message' => 'User is not assigned to an agent',
            ], Response::HTTP_FORBIDDEN);
        }

        Account::query()->create([
            'name' => $request->name,
            'number' => $request->number,
            'opening_balance' => $request->opening_balance,
            'current_balance' => $request->opening_balance,
            'agent_id' => $agent->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Account Created Successful.',
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

    public function deposit(Request $request, string $id)
    {
        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:10'
        ]);

        $account = Account::query()->findOrFail($id);

        // Log Transaction
        Transaction::query()->create([
            'sender_id' => $account->id,
            'receiver_id' => null,
            'type' => PaymentType::DEPOSIT,
            'amount' => $request->amount,
            'commission' => $request->commission,
            'reference' => $request->reference,
            'remark' => $request->remark,
            'status' => PaymentStatus::COMPLETED
        ]);

        // Balance Update
        $account->current_balance += $request->amount;
        $account->save();

        return response()->json([
            'success' => true,
            'message' => 'Deposit Successful.'
        ], Response::HTTP_CREATED);
    }


    public function withdraw(Request $request, string $id)
    {
        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:10'
        ]);

        $account = Account::query()->findOrFail($id);

        if ($account->current_balance < $request->amount) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient balance!'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Log Transaction
        Transaction::query()->create([
            'sender_id' => $account->id,
            'receiver_id' => null,
            'type' => $request->type,
            'amount' => $request->amount,
            'commission' => $request->commission,
            'remark' => $request->remark,
            'status' => PaymentStatus::COMPLETED
        ]);

        // Balance Update
        $account->current_balance -= $request->amount;
        $account->save();

        return response()->json([
            'success' => true,
            'message' => 'Withdraw Successful.'
        ], Response::HTTP_CREATED);
    }

    public function exchange(Request $request)
    {
        $request->validate([
            'sender_id' => 'required|exists:accounts,id',
            'receiver_id' => 'required|exists:accounts,id|different:sender_id',
            'amount' => 'required|numeric|min:10'
        ]);

        $sender = Account::query()->findOrFail($request->sender_id);
        $receiver = Account::query()->findOrFail($request->receiver_id);

        if ($sender->current_balance < $request->amount) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient balance!'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Log Transaction
        Transaction::query()->create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'type' => PaymentType::EXCHANGE,
            'amount' => $request->amount,
            'fee' => $request->fee,
            'reference' => $request->reference,
            'remark' => $request->remark,
            'status' => PaymentStatus::COMPLETED
        ]);

        // Balance Update
        $sender->current_balance -= $request->amount;
        $receiver->current_balance += $request->amount;

        $sender->save();
        $receiver->save();

        return response()->json([
            'success' => true,
            'message' => 'Balance Exchange Successful!'
        ], Response::HTTP_CREATED);
    }


    public function transactions(Request $request)
    {
        $transactions = Transaction::query()
            ->with(['sender', 'receiver', 'creator'])
            ->latest()
            ->whereDate('created_at', '=', Carbon::parse($request->date)->toDateString())
            ->get();
        return TransactionResource::collection($transactions);
    }

    public function statement(Request $request, string $id)
    {

        $account = Account::query()
            ->with(['transactions'])
            ->findOrFail($id);

        $transactions = $account->transactions()
            ->whereDate('created_at', '=', Carbon::parse($request->date)->toDateString())
            ->get();

        return TransactionResource::collection($transactions)->additional([
            'account' => AccountResource::make($account),
        ]);
    }
}
