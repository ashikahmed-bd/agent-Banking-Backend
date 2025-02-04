<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProfitController extends Controller
{
    public function getProfit()
    {
        $todayProfit = Transaction::query()
            ->whereDate('date', '=' , Carbon::parse(now())->toDateString())
            ->sum('profit');

        $totalProfit = Transaction::query()->sum('profit');

        return response()->json([
            'today' => $todayProfit,
            'total' => $totalProfit,
        ], Response::HTTP_OK);

    }

    public function getTotalProfit()
    {

    }
}
