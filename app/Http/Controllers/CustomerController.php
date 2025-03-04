<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Company;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Resources\PaymentResource;
use App\Http\Resources\CustomerResource;
use Symfony\Component\HttpFoundation\Response;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Company $company)
    {
        $customers = $company->customers()
            ->orderBy('name', 'asc')
            ->paginate($request->limit);
        return CustomerResource::collection($customers);
    }

    /**
     * Store a newly created resource in storage.
     * @throws Exception
     */
    public function store(Request $request, Company $company)
    {

        // Ensure the authenticated user belongs to this company
        if (!$request->user()->companies()->where('companies.id', $company->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to this company!',
            ], Response::HTTP_FORBIDDEN);
        }

        $customer = $company->customers()->create([
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address ?? null,
        ]);

        // Handle due amount (debit)
        if (!empty($request->due) && $request->due > 0){
            $customer->withdraw($request->due);
            $customer->payments()->create([
                'credit' => false,
                'amount' => $request->due,
                'remark' => $request->remark ?? null,
            ]);
        }

        // Handle payable amount (credit)
        if (!empty($request->payable) && $request->payable > 0) {
            $customer->deposit($request->payable);
            $customer->payments()->create([
                'credit' => true,
                'amount' => $request->payable,
                'remark' => $request->remark ?? null,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Customer Added Successful.',
        ], Response::HTTP_CREATED);
    }


    public function show(Company $company, string $id)
    {
        $customer = $company->customers()->findOrFail($id);
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
                'credit' => false,
                'amount' => $request->due,
                'remark' => $request->remark,
            ]);
        }

        if ($request->payable > 0){
            $customer->deposit($request->payable);
            $customer->payments()->create([
                'credit' => true,
                'amount' => $request->payable,
                'remark' => $request->remark,
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
