<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class IgrejaController extends Controller
{
    public function buscar(Request $request)
    {
        $q = $request->query('q', '');

        $igrejas = Igreja::query()
            ->where('name', 'like', "%{$q}%")
            ->limit(10)
            ->get(['id', 'name']);

        return response()->json($igrejas);
    }

}
