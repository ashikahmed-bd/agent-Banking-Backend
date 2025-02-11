<?php

namespace App\Http\Controllers;

use App\Enums\PaymentType;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\PaymentResource;
use App\Models\Customer;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customers = Customer::query()->get();
        return CustomerResource::collection($customers);
    }

    /**
     * Store a newly created resource in storage.
     * @throws \Exception
     */
    public function store(Request $request)
    {
        $customer = new Customer();
        $customer->name = $request->name;
        $customer->phone = $request->phone;
        $customer->save();

        if ($request->due > 0){
            $customer->withdraw($request->due);
            $customer->payments()->create([
                'type' => PaymentType::DEBIT,
                'amount' => $request->due,
                'note' => $request->note ?? null,
            ]);
        }

        if ($request->payable > 0) {
            $customer->deposit($request->payable);
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
            $customer->withdraw($request->due);
            $customer->payments()->create([
                'type' => PaymentType::DEBIT,
                'amount' => $request->due,
                'note' => $request->note,
            ]);
        }

        if ($request->payable > 0){
            $customer->deposit($request->payable);
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
    public function getReport(string $id)
    {
        $customer = Customer::query()->findOrFail($id);
        $payments = $customer->payments()->with(['user'])->orderBy('created_at')->get();

        return PaymentResource::collection($payments);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function getTotalBalance()
    {
        $total_due = Customer::query()
            ->where('balance', '<', 0) // Customers who owe money
            ->sum('balance');

        $total_payable = Customer::query()
            ->where('balance', '>', 0) // Customers with extra balance
            ->sum('balance');

        return [
            'total_due' => $total_due,
            'total_payable' => $total_payable,
        ];
    }


    public function destroy(string $id)
    {
        $customer = Customer::query()->findOrFail($id);

        if ($customer->balance <> 0){
            return response()->json([
                'success' => false,
                'message' => 'Customer can`t be deleted. this customer balance exists',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $customer->delete();

        return response()->json([
            'success' => true,
            'message' => 'Customer deleted successful',
        ], Response::HTTP_OK);
    }
}
