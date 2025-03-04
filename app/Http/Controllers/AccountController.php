<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Models\Company;
use App\Enums\PaymentType;
use App\Models\Transaction;
use App\Enums\PaymentStatus;
use Illuminate\Http\Request;
use App\Http\Resources\AccountResource;
use App\Http\Resources\TransactionResource;
use Symfony\Component\HttpFoundation\Response;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Company $company)
    {
        $accounts = $company->accounts()->get();
        return AccountResource::collection($accounts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Company $company)
    {
        $request->validate([
            'name' => ['required', 'string'],
            'number' => ['required', 'string'],
            'opening_balance' => ['required', 'numeric', 'min:1'],
        ]);

        $company->accounts()->create([
            'name' => $request->name,
            'number' => $request->number,
            'opening_balance' => $request->opening_balance,
            'current_balance' => $request->opening_balance,
            'company_id' => $company->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Account Created Successful.',
        ], Response::HTTP_CREATED);
    }

    public function deposit(Request $request, Company $company, string $id)
    {
        $request->validate([
            'amount' => ['required', 'numeric', 'min:10'],
        ]);

        // Ensure the account belongs to the specified company
        $account = $company->accounts()->where('id', $id)->first();

        if (!$account) {
            return response()->json([
                'success' => false,
                'message' => 'Account not found in this company!'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Ensure fee is not greater than the deposit amount
        if ($request->fee >= $request->amount) {
            return response()->json([
                'success' => false,
                'message' => 'Fee cannot be greater than or equal to the deposit amount.'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Log Transaction
        Transaction::query()->create([
            'sender_id' => $account->id,
            'receiver_id' => null,
            'company_id' => $company->id,
            'type' => PaymentType::DEPOSIT,
            'amount' => $request->amount,
            'fee' => $request->fee ?? 0,
            'remark' => $request->remark,
            'status' => PaymentStatus::COMPLETED
        ]);

        // Update account balance
        $account->increment('current_balance', $request->amount);

        return response()->json([
            'success' => true,
            'message' => 'Deposit Successful.'
        ], Response::HTTP_CREATED);
    }


    public function withdraw(Request $request, Company $company, string $id)
    {
        $request->validate([
            'amount' => ['required', 'numeric', 'min:10'],
        ]);

        // Ensure the account belongs to the specified company
        $account = $company->accounts()->where('id', $id)->first();

        if (!$account) {
            return response()->json([
                'success' => false,
                'message' => 'Account not found in this company!'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Ensure fee is not greater than the deposit amount
        if ($request->fee >= $request->amount) {
            return response()->json([
                'success' => false,
                'message' => 'Fee cannot be greater than or equal to the deposit amount.'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Check balance
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
            'company_id' => $company->id,
            'type' => PaymentType::WITHDRAW, // Default to withdrawal type
            'amount' => $request->amount,
            'fee' => $request->fee ?? 0, // Ensure default value for fee
            'remark' => $request->remark,
            'status' => PaymentStatus::COMPLETED
        ]);

        // Balance Update
        $account->decrement('current_balance', $request->amount);

        return response()->json([
            'success' => true,
            'message' => 'Withdraw Successful.'
        ], Response::HTTP_CREATED);
    }

    public function exchange(Request $request, Company $company)
    {
        $request->validate([
            'sender_id' => ['required', 'exists:accounts,id'],
            'receiver_id' => ['required', 'exists:accounts,id', 'different:sender_id'],
            'amount' => ['required', 'numeric', 'min:10'],
        ]);

        // Ensure both accounts belong to the given company
        $sender = $company->accounts()->where('id', $request->sender_id)->first();
        $receiver = $company->accounts()->where('id', $request->receiver_id)->first();

        if (!$sender || !$receiver) {
            return response()->json([
                'success' => false,
                'message' => 'One or both accounts do not belong to this company!'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Ensure fee is not greater than the deposit amount
        if ($request->fee >= $request->amount) {
            return response()->json([
                'success' => false,
                'message' => 'Fee cannot be greater than or equal to the deposit amount.'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Check sender's balance
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
            'company_id' => $company->id,
            'type' => PaymentType::EXCHANGE,
            'amount' => $request->amount,
            'fee' => $request->fee ?? 0,
            'reference' => $request->reference,
            'remark' => $request->remark,
            'status' => PaymentStatus::COMPLETED
        ]);


        // Update Balances
        $sender->decrement('current_balance', $request->amount);
        $receiver->increment('current_balance', $request->amount);

        return response()->json([
            'success' => true,
            'message' => 'Balance Exchange Successful!'
        ], Response::HTTP_CREATED);
    }

    public function getBalance(Company $company)
    {
        $balance = $company->accounts()
            ->sum('current_balance');

        return response()->json([
            'success' => true,
            'balance' => $balance,
        ], Response::HTTP_OK);
    }


    public function transactions(Request $request, Company $company)
    {
        $request->validate([
            'date' => ['nullable','date'],
        ]);

        // Set the date to today's date if not provided
        $date = $request->date ? Carbon::parse($request->date)->toDateString() : Carbon::today()->toDateString();

        // Build the query
        $transactions = $company->transactions()
            ->with(['sender', 'receiver', 'creator'])
            ->whereDate('created_at', '=', $date)
            ->paginate($request->limit);

        return TransactionResource::collection($transactions);
    }

    public function statement(Request $request, Company $company, string $id)
    {
        // Validate the date input to ensure it is a valid date
        $request->validate([
            'date' => ['nullable', 'date'],
        ]);

        // Fetch the account and ensure it belongs to the company
        $account = $company->accounts()->find($id);

        if (!$account) {
            return response()->json([
                'success' => false,
                'message' => 'Account not found in this company!',
            ], Response::HTTP_BAD_REQUEST);
        }

        // Default date to today if not provided
        $date = $request->date ? Carbon::parse($request->date)->toDateString() : Carbon::today()->toDateString();

        // Get transactions for the given account and date
        $transactions = $account->transactions()
            ->whereDate('created_at', '=', $date)  // Filter by the specified date
            ->get();


        $pdf = Pdf::loadView('pdf.account_statement', [
            'title' => 'Account Statement',
            'company' => $company,
            'date' => Carbon::parse(now())->toFormattedDayDateString(),
            'account' => $account,
            'deposit' => $transactions->where('credit', true)->sum('amount'),
            'withdraw' => $transactions->where('credit', false)->sum('amount'),
            'transactions' => $transactions,
        ]);

        // Return the PDF as a download response
        return $pdf->download('statement_' . $account->id . '_' . $date . '.pdf');
    }


    public function income(Request $request, Company $company)
    {
        $request->validate([
            'date' => ['nullable', 'date'],
        ]);

        $date = $request->date ? Carbon::parse($request->date)->toDateString() : Carbon::today()->toDateString();

        $income = $company->transactions()
            ->whereDate('created_at', '=', $date)
            ->sum('fee');  // Calculate the sum of the 'fee' column

        return response()->json([
            'success' => true,
            'income' => $income,
        ], Response::HTTP_OK);
    }


    public function expense(Request $request, Company $company)
    {
        $request->validate([
            'date' => ['nullable', 'date'],
        ]);

        $date = $request->date ? Carbon::parse($request->date)->toDateString() : Carbon::today()->toDateString();

        $expense = $company->transactions()
            ->whereDate('created_at', '=', $date)
            ->where('type', '=', (PaymentType::EXPENSE)->value)
            ->sum('amount');

        return response()->json([
            'success' => true,
            'expense' => $expense,
        ], Response::HTTP_OK);
    }

}
