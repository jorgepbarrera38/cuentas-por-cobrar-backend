<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\models\Third;
use App\models\AccountReceivable;

class AccountReceivableController extends Controller
{
    public function getAccountReceivables () {
        $accountReceivables = AccountReceivable::when(request()->thirdId, function ($query, $thirdId) {
                                                                    $query->where('third_id', $thirdId);
                                                                })
                                                                //->where('third_id', request()->thirdId)
                                                                ->with('third:id,code,name')
                                                                ->orderBy('datetime', 'desc')->paginate(7);

        return $accountReceivables;
    }

    public function getDataToRegisterAccountReceivable () {
        $thirds = Third::orderBy('name', 'asc')->get();

        return [
            'thirds' => $thirds,
        ];
    }

    public function registerAccountReceivable (Request $request) {
        $data = $request->validate([
            'datetime' => 'required|date|before_or_equal:now',
            'third_id' => 'required|exists:thirds,id',
            'concept' => 'required|max:500',
            'charge' => 'required|min:1|max:100000000',
            'days_deadline' => 'required|numeric|min:1|max:365',
        ], [], [
            'datetime' => 'Fecha y hora',
            'third_id' => 'Tercero',
            'concept' => 'Concepto',
            'charge' => 'Cargo',
            'days_deadline' => 'Días de plazo',
        ]);

        AccountReceivable::create([
            'datetime' => $data['datetime'],
            'third_id' => $data['third_id'],
            'concept' => $data['concept'],
            'charge' => $data['charge'],
            'deposit' => 0,
            'days_deadline' => $data['days_deadline'],
            'balance' => $data['charge']
        ]);
    }

    public function updateAccountReceivable (Request $request, AccountReceivable $accountReceivable) {
        $data = $request->validate([
            'datetime' => 'required|date|before_or_equal:now',
            'concept' => 'required|max:500',
            'charge' => 'required|numeric|min:1|max:100000000',
            'days_deadline' => 'required|numeric|min:1|max:365',
        ], [], [
            'datetime' => 'Fecha y hora',
            'concept' => 'Concepto',
            'charge' => 'Cargo',
            'days_deadline' => 'Días de plaz',
        ]);

        if ($data['charge'] < $accountReceivable->deposit) {
            return response()->json(['errors' => ['charge' => ['El valor del cargo no puede ser menor al valor del abono']]], 422);
        }

        $data['balance'] = $data['charge'] - $accountReceivable->deposit;

        $accountReceivable->update([
            'datetime' => $data['datetime'],
            'concept' => $data['concept'],
            'charge' => $data['charge'],
            'days_deadline' => $data['days_deadline'],
            'balance' => $data['balance'],
        ]);
    }

    public function deleteAccountReceivable (AccountReceivable $accountReceivable) {
        if ($accountReceivable->deposit > 0) {
            return response()->json(['errors' => ['message' => ['La cuenta por cobrar no se puede eliminar ya que tiene abonos realizados']]], 422);
        }
        $accountReceivable->delete();
    }
}
