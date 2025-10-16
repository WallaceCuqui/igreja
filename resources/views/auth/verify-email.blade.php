@extends('layouts.app')

@section('title', 'Verifique seu E-mail')

@section('content')
<div class="container mx-auto max-w-md mt-20 bg-white p-6 rounded-xl shadow-md text-center">
    <h2 class="text-2xl font-bold mb-4">Verifique seu e-mail</h2>
    <p class="mb-4">
        Antes de continuar, verifique seu e-mail clicando no link que enviamos.
        Se você não recebeu o e-mail,
    </p>

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md">
            Reenviar e-mail de verificação
        </button>
    </form>
</div>
@endsection
