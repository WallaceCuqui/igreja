<x-layouts.app :title="'Protocolo #' . $protocolo->protocolo">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Cabeçalho -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-2">Protocolo #{{ $protocolo->protocolo }}</h1>
                    <p><strong>Status:</strong> {{ ucfirst($protocolo->status) }}</p>
                    <p><strong>Assunto:</strong> {{ $protocolo->assunto }}</p>
                </div>
            </div>

            <!-- Conversa -->
            <div class="bg-white p-6 rounded shadow">
                <h2 class="text-lg font-semibold mb-4">Mensagens</h2>

                <div class="space-y-4 max-h-96 overflow-y-auto p-2 bg-gray-50 rounded-lg">
                    @forelse ($protocolo->mensagens as $msg)
                        <div class="p-3 rounded-lg w-fit max-w-[80%] 
                            @if($msg->is_staff) bg-blue-100 ml-auto text-right @else bg-gray-200 mr-auto text-left @endif">
                            <p class="text-sm text-gray-800 whitespace-pre-line">{{ $msg->mensagem }}</p>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $msg->user?->name ?? 'Usuário' }} — {{ $msg->created_at->format('d/m/Y H:i') }}
                            </p>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm italic text-center">Nenhuma mensagem ainda.</p>
                    @endforelse
                </div>

                <!-- Enviar nova mensagem -->
                <form method="POST" action="{{ route('protocolo.responder', $protocolo) }}" class="mt-6">
                    @csrf
                    <div>
                        <textarea name="mensagem" rows="3" class="border p-2 w-full rounded"
                            placeholder="Digite sua mensagem..." required></textarea>
                        @error('mensagem') <p class="text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="mt-3 text-right">
                        <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                            Enviar resposta
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
