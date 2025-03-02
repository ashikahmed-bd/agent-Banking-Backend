<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Customer;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;

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

        $cash = Account::query()->get();

        $pdf = Pdf::loadView('pdf.transactions', [
            'title' => 'Daily Report',
            'cash' => $cash->where('default', '=', true)->sum('balance'),
            'accounts' => $cash->where('default', '=', false)->sum('balance'),
            'total_due' => $total_due,
            'total_payable' => $total_payable,
            'transactions' => $transactions,

        ]);

        return Response::make($pdf->download(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="payments.pdf"',
        ]);
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
            ->orderBy('name', 'asc')
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
            'customers' => $customers,
            'total_due' => $total_due,
            'total_payable' => $total_payable,
        ]);

        return Response::make($pdf->download(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="report.pdf"',
        ]);
    }


    public function getStatement(Request $request, string $id)
    {
        $account = Account::query()
            ->with(['transactions'])
            ->findOrFail($id);

        $transactions = $account->transactions()
            ->whereDate('created_at', '=', Carbon::parse($request->date)->toDateString())
            ->get();

        $pdf = Pdf::loadView('pdf.account_statement', [
        'title' => 'Account Statement',
        'date' => Carbon::parse(now())->toDateString(),
        'account' => $account,
        'transactions' => $transactions,
    ]);
        return Response::make($pdf->download(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="report.pdf"',
        ]);
    }

}
