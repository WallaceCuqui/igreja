@extends('layouts.app')

@section('title', 'Protocolo #' . $protocolo->numero)

@section('content')
    <div class="bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Protocolo #{{ $protocolo->numero }}</h1>

        <p><strong>Status:</strong> {{ ucfirst($protocolo->status) }}</p>
        <p><strong>Nome:</strong> {{ $protocolo->nome }}</p>
        <p><strong>E-mail:</strong> {{ $protocolo->email }}</p>

        <div class="mt-4">
            <h2 class="font-semibold">Mensagem:</h2>
            <p class="mt-2 text-gray-700 whitespace-pre-line">{{ $protocolo->mensagem }}</p>
        </div>

        <div class="mt-6">
            <a href="{{ route('protocolo.create') }}" class="text-blue-600 hover:underline">Abrir novo protocolo</a>
        </div>
    </div>
@endsection
