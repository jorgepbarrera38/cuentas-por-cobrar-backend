<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AccountReceivable;

class DashboardController extends Controller
{
    public function getDataFromDashboard () {
        $totalCharge = AccountReceivable::sum('charge');
        $totalDeposit = AccountReceivable::sum('deposit');
        $totalBalance = AccountReceivable::sum('balance');
        $mainBalance = AccountReceivable::join('thirds', 'account_receivables.third_id', 'thirds.id')
                                            ->groupBy('third_id', 'name')
                                            ->havingRaw('SUM(balance) > 0')
                                            ->select('third_id', 'name', \DB::raw('SUM(balance) as totalBalance'))
                                            ->orderBy('totalBalance', 'desc')
                                            ->take(5)
                                            ->get();

        return [
            'totalCharge' => $totalCharge,
            'totalDeposit' => $totalDeposit,
            'totalBalance' => $totalBalance,
            'mainBalance' => $mainBalance,
        ];
    }
}
