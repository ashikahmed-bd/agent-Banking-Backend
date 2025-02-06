<?php

namespace App\Http\Controllers;

use App\Enums\PaymentType;
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
        $customer = new Customer();
        $customer->name = $request->name;
        $customer->phone = $request->phone;

        if ($request->has('due')){
            $customer->balance -= $request->due;
        }
        if ($request->has('payable')){
            $customer->balance += $request->payable;
        }
        $customer->save();

        // Now customer_id exists, we can safely create the payment
        if ($request->due > 0){
            $customer->payments()->create([
                'type' => PaymentType::DEBIT,
                'amount' => $request->due,
                'note' => $request->note ?? null,
            ]);
        }

        // Now customer_id exists, we can safely create the payment
        if ($request->payable > 0) {
            $customer->payments()->create([
                'type' => PaymentType::CREDIT,
                'amount' => $request->payable,
                'note' => $request->note ?? null,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Customer Added Successful.',
        ], Response::HTTP_CREATED);
    }


    public function show(string $id)
    {
        $customer = Customer::query()->findOrFail($id);
        return CustomerResource::make($customer);
    }


    /**
     * Display the specified resource.
     */
    public function payment(Request $request, string $id)
    {

        $customer = Customer::query()->findOrFail($id);

        if ($request->due > 0){
            $customer->balance -= $request->due;

            $customer->payments()->create([
                'type' => PaymentType::DEBIT,
                'amount' => $request->due,
                'note' => $request->note,
            ]);
        }

        if ($request->payable > 0){
            $customer->balance += $request->payable;

            $customer->payments()->create([
                'type' => PaymentType::CREDIT,
                'amount' => $request->payable,
                'note' => $request->note,
            ]);
        }

        $customer->update();

        return response()->json([
            'success' => true,
            'message' => 'Payments Successful.',
        ], Response::HTTP_CREATED);
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
