<?php

namespace App\Http\Controllers;

use App\Http\Resources\CompanyResource;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $companies = Auth::user()->companies()->get();
        return response()->json($companies);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string'],
            'phone' => ['required', 'string', Rule::unique('companies', 'phone')],
        ]);


        $company = Company::query()->create([
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        $company->accounts()->create([
            'name' => "Cash",
            'number' => "1",
            'logo' => "cash.svg",
            'balance' => 0,
            'default' => true,
        ]);

        // Attach company to user logged in
        $request->user()->companies()->attach($company);

        return response()->json([
            'success' => true,
            'message' => 'Company Created Successful.',
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
