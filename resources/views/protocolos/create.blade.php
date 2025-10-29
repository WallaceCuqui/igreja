<x-layouts.app title="Abrir Protocolo">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Caixa de instrução -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    {{ __("Preencha o formulário abaixo para abrir um novo protocolo.") }}
                </div>
            </div>

            <!-- Mensagem de sucesso -->
            @if (session('success'))
                <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Formulário -->
            <div class="bg-white p-6 rounded shadow">
                <form method="POST" action="{{ route('protocolo.store') }}">
                    @csrf

                    <div class="mb-4">
                        <label class="block font-semibold">Nome</label>
                        <input type="text" name="nome" value="{{ $user->name ?? old('nome') }}" class="border p-2 w-full rounded">
                        @error('nome') <p class="text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-semibold">E-mail</label>
                        <input type="email" name="email" value="{{ $user->email ?? old('email') }}" class="border p-2 w-full rounded">
                        @error('email') <p class="text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-semibold">Assunto</label>
                        <input type="text" name="assunto" value="{{ old('assunto') }}" class="border p-2 w-full rounded">
                        @error('assunto') <p class="text-red-600">{{ $message }}</p> @enderror
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
            </div>
        </div>
    </div>

    <!-- Lista de chamados do usuário -->
<div class="bg-white p-6 rounded shadow mt-6">
    <h2 class="text-lg font-semibold mb-4">Meus chamados</h2>

    @if($protocolos->isEmpty())
        <div class="text-sm text-gray-500">Você ainda não abriu nenhum protocolo.</div>
    @else
        <div class="space-y-3">
            @foreach($protocolos as $p)
                <div class="flex items-center justify-between p-3 border rounded">
                    <div>
                        <div class="text-sm text-gray-600">Protocolo: <strong>{{ $p->protocolo ?? $p->id }}</strong></div>
                        <div class="font-medium">{{ $p->assunto }}</div>
                        <div class="text-xs text-gray-500">Criado em {{ $p->created_at->format('d/m/Y H:i') }}</div>
                    </div>

                    <div class="flex items-center space-x-4">
                        @php
                            $statusLabels = [
                                'aberto' => ['label' => 'Aberto', 'class' => 'bg-blue-100 text-blue-800'],
                                'em_atendimento' => ['label' => 'Em atendimento', 'class' => 'bg-yellow-100 text-yellow-800'],
                                'concluido' => ['label' => 'Concluído', 'class' => 'bg-green-100 text-green-800'],
                                'cancelado' => ['label' => 'Cancelado', 'class' => 'bg-red-100 text-red-800'],
                            ];
                            $s = $statusLabels[$p->status] ?? ['label' => $p->status, 'class' => 'bg-gray-100 text-gray-800'];
                        @endphp

                        <span class="px-2 py-1 text-xs rounded {{ $s['class'] }}">{{ $s['label'] }}</span>

                        <a href="{{ route('protocolo.show', $p) }}" class="text-sm text-blue-600 hover:underline">Ver</a>
                    </div>
                </div>
            @endforeach
        </div>

        @if(method_exists($protocolos, 'links'))
            <div class="mt-4">
                {{ $protocolos->links() }}
            </div>
        @endif
    @endif
</div>

</x-layouts.app>
