@extends('layouts.app')

@section('title', 'Abrir Protocolo')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Abrir Protocolo</h1>

    @if (session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('protocolo.store') }}" class="bg-white p-6 rounded shadow">
        @csrf

        <div class="mb-4">
            <label class="block font-semibold">Nome</label>
            <input type="text" name="nome" value="{{ old('nome') }}" class="border p-2 w-full rounded">
            @error('nome') <p class="text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block font-semibold">E-mail</label>
            <input type="email" name="email" value="{{ old('email') }}" class="border p-2 w-full rounded">
            @error('email') <p class="text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block font-semibold">Mensagem</label>
            <textarea name="mensagem" rows="5" class="border p-2 w-full rounded">{{ old('mensagem') }}</textarea>
            @error('mensagem') <p class="text-red-600">{{ $message }}</p> @enderror
        </div>

        <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
            Enviar
        </button>
    </form>
@endsection
