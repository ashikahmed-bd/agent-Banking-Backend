<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function process(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'type' => 'required|in:deposit,withdrawal,transfer,expense',
            'amount' => 'required|numeric|min:0.01',
        ]);

        $account = Account::query()->find($request->account_id);

        if ($request->type === 'withdrawal' && $account->current_balance < $request->amount) {
            return response()->json(['error' => 'Insufficient funds'], 400);
        }

        // Update the current balance
        $newBalance = ($request->type === 'deposit')
            ? $account->current_balance + $request->amount
            : $account->current_balance - $request->amount;

        $account->update(['current_balance' => $newBalance]);

        // Record the transaction
        Transaction::query()->create([
            'account_id' => $account->id,
            'type' => $request->type,
            'amount' => $request->amount,
        ]);

        return response()->json([
            'message' => 'Transaction processed successfully',
            'current_balance' => $newBalance
        ]);
    }
}
