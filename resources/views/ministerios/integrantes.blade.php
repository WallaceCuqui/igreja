<x-layouts.app title="Integrantes do Ministério: {{ $ministerio->nome }}">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Integrantes do Ministério:
            <a href="{{ route('ministerios.show', $ministerio->id) }}" class="text-indigo-600">{{ $ministerio->nome }}</a>
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-md">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <p class="text-gray-700 mb-4">
                Selecione os membros que fazem parte deste ministério e defina o status de participação.
            </p>

                <div class="mb-4">
                    <x-input-label for="filtro" value="Filtrar membros" />
                    <input type="text" id="filtro" placeholder="Digite o nome para filtrar..."
                        class="w-full mt-1 rounded-md border-gray-300 shadow-sm"
                        oninput="filtrarMembros(this.value)">
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200 divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Selecionar</th>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Nome</th>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Status</th>
                            </tr>
                        </thead>
                        <tbody id="tabela-membros" class="divide-y divide-gray-100">
                            @foreach ($membros as $m)
                                @php
                                    $checked = in_array($m->id, $integrantesAtuais ?? []) ? 'checked' : '';
                                    $statusAtual = $statusAtuais[$m->id] ?? old("status.$m->id", 'ativo');
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2">{{ $m->name }}</td>
                                    <td class="px-4 py-2 text-center">
                                        <input type="checkbox"
                                            class="status-toggle"
                                            data-url-ativar="{{ route('ministerios.integrantes.ativar', [$ministerio->id, $m->id]) }}"
                                            data-url-remover="{{ route('ministerios.integrantes.remover', [$ministerio->id, $m->id]) }}"
                                            {{ ($statusAtuais[$m->id] ?? '') === 'ativo' ? 'checked' : '' }}
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

        </div>
    </div>

    <script>
        function filtrarMembros(valor) {
            const linhas = document.querySelectorAll("#tabela-membros tr");
            valor = valor.toLowerCase();
            linhas.forEach(linha => {
                const nome = (linha.children[1] && linha.children[1].textContent) ? linha.children[1].textContent.toLowerCase() : '';
                linha.style.display = nome.includes(valor) ? "" : "none";
            });
        }
    </script>
</x-layouts.app>
