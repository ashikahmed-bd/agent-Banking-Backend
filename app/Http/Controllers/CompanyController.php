<?php

namespace App\Http\Controllers;

use App\Http\Resources\CompanyResource;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        // Check if user is linked to the selected company
        if (!$user->companies()->exists()) {
            return response()->json([
                'success' => false,
                'message' => trans('messages.no_company')
            ], Response::HTTP_FORBIDDEN);
        }

        $companies = $user->companies()
            ->orderBy('default', 'desc')
            ->get();

        return CompanyResource::collection($companies);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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


    public function default()
    {
        $user = Auth::user();

        $company = $user->companies()
            ->where('default', '=', true)
            ->firstOrFail();

        return CompanyResource::make($company);
    }
}
