<?php

namespace App\Http\Controllers;

use App\Http\Resources\AccountResource;
use App\Http\Resources\BusinessResource;
use App\Models\Account;
use App\Models\Business;
use App\Models\Customer;
use Symfony\Component\HttpFoundation\Response;

class BusinessController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getOwner()
    {
        $business = Business::with('owner')->findOrFail(auth()->id());
        return BusinessResource::make($business);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function getAccounts()
    {
        $business = Business::query()->where('id', auth()->user()->business->id)
            ->with('accounts')
            ->firstOrFail();

        $accounts = $business->accounts()
            ->where('default', '=', false)
            ->get();

        return AccountResource::collection($accounts);
    }

    /**
     * Display the specified resource.
     */
    public function getBalance()
    {
        $account = Account::query()
            ->where('business_id', auth()->user()->business->id)
            ->get();


        return response()->json([
            'cash' => $account->where('default', '=', true)->sum('balance'),
            'wallet' => $account->where('default', '=', false)->sum('balance'),
            'total_due' => $account->where('default', '=', false)->sum('balance'),
        ], Response::HTTP_OK);
    }




}
