<x-layouts.app :title="'Protocolo #' . $protocolo->numero">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Caixa superior -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    {{ __("Detalhes do protocolo aberto.") }}
                </div>
            </div>

            <!-- Caixa do protocolo -->
            <div class="bg-white p-6 rounded shadow">
                <h1 class="text-2xl font-bold mb-4">Protocolo #{{ $protocolo->protocolo }}</h1>
                <p><strong>Status:</strong> {{ ucfirst($protocolo->status) }}</p>
                <p><strong>Nome:</strong> {{ $protocolo->nome }}</p>
                <p><strong>E-mail:</strong> {{ $protocolo->email }}</p>
                <p><strong>Assunto:</strong> {{ $protocolo->assunto }}</p>
                <div class="mt-4">
                    <h2 class="font-semibold">Mensagem:</h2>
                    <p class="mt-2 text-gray-700 whitespace-pre-line">{{ $protocolo->mensagem }}</p>
                </div>
                <div class="mt-6">
                    <a href="{{ route('protocolo.create') }}" class="text-blue-600 hover:underline">Abrir novo protocolo</a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
