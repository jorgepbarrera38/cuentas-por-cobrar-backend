<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Third;

class ThirdController extends Controller
{
    public function registerThird (Request $request) {
        $data = $request->validate([
            'code' => 'required|max:20|unique:thirds,code',
            'name' => 'required|max:60|unique:thirds,name',
        ], [], [
            'code' => 'Código',
            'name' => 'Nombre'
        ]);

        Third::create($data);

    }

    public function updateThird (Request $request, Third $third) {
        $data = $request->validate([
            'code' => 'required|max:20|unique:thirds,code,' . $third->id,
            'name' => 'required|max:60|unique:thirds,name,' . $third->id,
        ], [], [
            'code' => 'Código',
            'name' => 'Nombre'
        ]);

        $third->update($data);

    }
}
