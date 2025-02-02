<?php

namespace App\Http\Controllers;

use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $customers = Customer::query()->paginate($request->limit);
        return CustomerResource::collection($customers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $customer = Customer::query()->create([
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address ?? null,
        ]);

        $customer->payments()->create([
            'receivable' => 400,
            'payable' => 100,
            'note' => 'Bank Transfer',

        ]);


        return response()->json([
            'success' => true,
            'message' => 'Customer Added Successful.',
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
}
