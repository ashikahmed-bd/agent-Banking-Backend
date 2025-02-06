<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends Controller
{
    public function getTransactionsPrint()
    {

        $transactions = Transaction::query()
            ->with(['account'])
            ->whereDate('date', '=', Carbon::parse(now())->toDateString())
            ->latest()->get();

        $pdf = Pdf::loadView('pdf.transactions', [
            'title' => 'Daily Report',
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

        return $transactions;

        $pdf = Pdf::loadView('pdf.transactions', [
            'title' => 'Daily Report',
            'account' => $account,
        ]);
        return $pdf->download('invoice.pdf');
    }
}
