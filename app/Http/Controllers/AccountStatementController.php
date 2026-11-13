<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Third;
use App\Models\AccountReceivable;
use App\Models\Deposit;

class AccountStatementController extends Controller
{
    public function getThirds () {
        return Third::leftJoin('account_receivables', 'thirds.id', '=', 'account_receivables.third_id')
                        ->select('thirds.id', 'thirds.code', 'thirds.name', \DB::raw('sum(account_receivables.balance) as totalBalance'))
                        ->groupBy('thirds.id', 'thirds.code', 'thirds.name')
                        ->orderBy('thirds.name')
                        ->get();
        return Third::orderBy('name', 'asc')->get();
    }

    public function getAccountReceivablesPendingsFromThird ($third) {
        $accountReceivables = AccountReceivable::where('third_id', $third)->orderBy('datetime', 'asc')->get();
        $deposits = Deposit::where('third_id', $third)->orderBy('datetime', 'asc')->get();

        return [
            'accountReceivables' => $accountReceivables,
            'deposits' => $deposits
        ];
    }

    public function registerDeposit (Request $request, Third $third) {

        $data = $request->validate([
            'deposit' => 'required|numeric|min:1|max:100000000',
            'observations' => 'required|max:200'
        ], [], [
            'deposit' => 'Valor del abono',
            'observations' => 'Observaciones',
        ]);

        $depositTemp = $data['deposit'];

        $accountsReceivableWithBalance = AccountReceivable::where('third_id', $third->id)->where('balance', '>', '0')->orderBy('datetime', 'asc')->get();

        $totalBalance = $accountsReceivableWithBalance->reduce(function ($carry, $item) {
            return $carry + $item['balance'];
        });

        if ($totalBalance < 1) {
            return response()->json(['errors' => ['deposit' => ['El tercero no tiene saldos pendientes']]], 422);
        }

        if ($data['deposit'] > $totalBalance) {
            return response()->json(['errors' => ['deposit' => ['El valor del abono supera el saldo total']]], 422);
        }

        try {
            \DB::beginTransaction();
    
            foreach ($accountsReceivableWithBalance as $accountReceivableWithBalance) {
    
    
                if ($depositTemp >= $accountReceivableWithBalance->balance) {
    
                    $depositTemp = $depositTemp - $accountReceivableWithBalance->balance;
                    $accountReceivableWithBalance->update(['deposit' => $accountReceivableWithBalance->charge]);
                    $accountReceivableWithBalance->update(['balance' => 0]);
    
                } else {
                    
                    $accountReceivableWithBalance->update(['deposit' => $accountReceivableWithBalance->deposit + $depositTemp]);
                    $accountReceivableWithBalance->update(['balance' => $accountReceivableWithBalance->balance - $depositTemp]);
                    $depositTemp = 0;
    
                }
    
                if ($depositTemp == 0) {
                    break;
                }
            }
    
            Deposit::create([
                'third_id' => $third->id,
                'datetime' => now(),
                'observations' => $data['observations'],
                'total' => $data['deposit'],
            ]);

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['errors' => ['deposit' => [$e->getMessage()]]], 422);
        }
    }

    public function deleteDeposit (Deposit $deposit) {

        $totalDepositTemp = $deposit->total;

        $accountsReceivableWithoutBalance = AccountReceivable::where('third_id', $deposit->third_id)->where('deposit', '>', '0')->orderBy('datetime', 'desc')->get();

        try {
            \DB::beginTransaction();
    
            foreach ($accountsReceivableWithoutBalance as $accountReceivableWithoutBalance) {
    
                if ($totalDepositTemp >= $accountReceivableWithoutBalance->deposit) {
    
                    $totalDepositTemp = $totalDepositTemp - $accountReceivableWithoutBalance->deposit;
                    $accountReceivableWithoutBalance->update(['deposit' => 0]);
                    $accountReceivableWithoutBalance->update(['balance' => $accountReceivableWithoutBalance->charge]);
    
                } else {
                    
                    $accountReceivableWithoutBalance->update(['deposit' => $accountReceivableWithoutBalance->deposit - $totalDepositTemp]);
                    $accountReceivableWithoutBalance->update(['balance' => $accountReceivableWithoutBalance->balance + $totalDepositTemp]);
                    $totalDepositTemp = 0;
    
                }
    
                if ($totalDepositTemp == 0) {
                    break;
                }
            }
    
            $deposit->delete();

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['errors' => ['deposit' => [$e->getMessage()]]], 422);
        }
    }
}
