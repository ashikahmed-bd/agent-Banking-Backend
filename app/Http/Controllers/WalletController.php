<?php

namespace App\Http\Controllers;

use App\Exceptions\InsufficientBalance;
use App\Models\Account;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WalletController extends Controller
{
    public function deposit(Request $request)
    {
        $cash = Account::query()->where('default', '=', true)->firstOrFail();
        $cash->deposit($request->amount);

        return response()->json([
            'success' => true,
            'message' => 'Cash Deposit Successful.',
        ], Response::HTTP_OK);
    }


    /**
     * @throws InsufficientBalance
     */
    public function withdraw(Request $request)
    {
        $cash = Account::query()->where('default', '=', true)->firstOrFail();
        $cash->withdraw($request->amount);

        return response()->json([
            'success' => true,
            'message' => 'Cash Withdraw Successful.',
        ], Response::HTTP_OK);
    }
}
