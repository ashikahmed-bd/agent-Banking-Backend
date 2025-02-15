<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Customer;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class PdfController extends Controller
{
    public function getTransactionsPrint()
    {

        $transactions = Transaction::query()
            ->with(['account'])
            ->whereDate('created_at', '=', Carbon::parse(now())->toDateString())
            ->latest()->get();


        $total_due = Customer::query()
            ->where('balance', '<', 0) // Customers who owe money
            ->sum('balance');

        $total_payable = Customer::query()
            ->where('balance', '>', 0) // Customers with extra balance
            ->sum('balance');

        $pdf = Pdf::loadView('pdf.transactions', [
            'title' => 'Daily Report',
            'company' => Auth::user()->companies()->get(),
            'total_due' => $total_due,
            'total_payable' => $total_payable,
            'transactions' => $transactions,

        ]);
        return $pdf->download('invoice.pdf');
    }


    public function getHistory(Request $request, string $id)
    {

        $account = Account::query()
            ->with(['transactions'])
            ->findOrFail($id);

        $transactions = Transaction::query()
            ->whereDate('date', '=', Carbon::parse($request->date)->toDateString())
            ->latest()->get();


        $pdf = Pdf::loadView('pdf.transactions', [
            'title' => 'Daily Report',
            'account' => $account,
        ]);
        return $pdf->download('invoice.pdf');
    }


    public function getCustomers()
    {
        $customers = Customer::query()
            ->with(['payments'])
            ->get();

        $total_due = Customer::query()
            ->where('balance', '<', 0) // Customers who owe money
            ->sum('balance');

        $total_payable = Customer::query()
            ->where('balance', '>', 0) // Customers with extra balance
            ->sum('balance');

        $pdf = Pdf::loadView('pdf.customers_report', [
            'title' => 'Customers Report',
            'date' => Carbon::parse(now())->toDateString(),
            'company' => Auth::user()->companies()->get(),
            'customers' => $customers,
            'total_due' => $total_due,
            'total_payable' => $total_payable,
        ]);
        return $pdf->download('customers-report.pdf');
    }
}
