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
            {{-- ✅ FORMULÁRIO DE ADIÇÃO DE INTEGRANTE --}}
            <h3 class="text-lg font-semibold mb-4">
                Integrantes do Ministério: 
                : <a href="{{ route('ministerios.show', $ministerio->id) }}" class="text-indigo-600">{{ $ministerio->nome }}</a>
            </h3>

            <div class="relative">
                <x-input-label value="Buscar Membro para adicionar" />

                <input type="text"
                    class="busca-dinamica border-gray-300 rounded-md w-full"
                    placeholder="Digite o nome do membro..."
                    data-endpoint="{{ route('membro.busca') }}?ministerio_id={{ $ministerio->id }}"
                    data-results="#resultados-membro"
                    data-ativar-url-template="{{ route('ministerios.integrantes.ativar', [$ministerio->id, 'MEMBRO_ID']) }}">

                <div id="resultados-membro" class="resultados-dinamicos absolute bg-white border border-gray-300 rounded-md w-full mt-1 max-h-40 overflow-y-auto shadow-lg z-10"></div>
            </div>


                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200 divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Nome</th>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Status</th>
                                <th class="px-4 py-2 text-center text-sm font-semibold text-gray-600">Aprovado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($integrantes as $m)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2">{{ $m->name }}</td>
                                    <td class="px-4 py-2 capitalize">
                                        {{ $m->pivot->status ?? '—' }}
                                    </td>
                                    <td class="px-4 py-2 text-center">
                                        <input type="checkbox"
                                            class="status-toggle"
                                            data-url-ativar="{{ route('ministerios.integrantes.ativar', [$ministerio->id, $m->id]) }}"
                                            data-url-remover="{{ route('ministerios.integrantes.remover', [$ministerio->id, $m->id]) }}"
                                            {{ $m->pivot->status === 'ativo' ? 'checked' : '' }}>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-4 text-center text-gray-500">
                                        Nenhum integrante cadastrado neste ministério.
                                    </td>
                                </tr>
                            @endforelse
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
