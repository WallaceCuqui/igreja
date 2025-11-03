<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class IgrejaController extends Controller
{
    public function buscar(Request $request)
    {
        $query = $request->get('q');

        Log::info('🔍 Buscando igrejas com termo: ' . $query);

        $igrejas = User::where('type', 'igreja')
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                ->orWhereHas('detalhesUsuario', function ($sub) use ($query) {
                    $sub->where('nome_fantasia', 'like', "%{$query}%");
                });
            })
            ->with('detalhesUsuario:id,user_id,nome_fantasia') // carrega apenas o necessário
            ->limit(10)
            ->get(['id', 'name']); // só id e name do User


        // Monta a lista formatada
        $resultado = $igrejas->map(function ($igreja) {
            $nomeFantasia = $igreja->detalhesUsuario->nome_fantasia ?? null;
            return [
                'id' => $igreja->id,
                'nome' => $nomeFantasia ?: $igreja->name,
            ];
        });


        Log::info('✅ Igrejas encontradas:', $resultado->toArray());

        return response()->json($resultado);
    }
}
