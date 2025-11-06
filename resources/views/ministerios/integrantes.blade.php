<x-layouts.app title="Integrantes dos Ministérios">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Integrantes do Ministério: ') . $ministerio->nome }}
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
                Selecione os membros que fazem parte deste ministério.
            </p>

            <form method="POST" action="{{ route('ministerios.integrantes.store', $ministerio->id) }}">
                @csrf

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
                                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Tipo de Vínculo</th>
                            </tr>
                        </thead>
                        <tbody id="tabela-membros" class="divide-y divide-gray-100">
                            @foreach ($membros as $m)
                                @php
                                    // $integrantesAtuais = array de ids; $vinculosAtuais = [id => tipo_vinculo]
                                    $checked = in_array($m->id, $integrantesAtuais ?? []) ? 'checked' : '';
                                    $tipoAtual = $vinculosAtuais[$m->id] ?? old("tipo_vinculo.$m->id", 'ativo');
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 text-center">
                                        <input type="checkbox" name="membros[]" value="{{ $m->id }}" {{ $checked }}
                                            class="rounded border-gray-300">
                                    </td>
                                    <td class="px-4 py-2">{{ $m->name }}</td>
                                    <td class="px-4 py-2">
                                        <select name="tipo_vinculo[{{ $m->id }}]" class="border-gray-300 rounded-md text-sm">
                                            <option value="ativo" {{ $tipoAtual === 'ativo' ? 'selected' : '' }}>Ativo</option>
                                            <option value="auxiliar" {{ $tipoAtual === 'auxiliar' ? 'selected' : '' }}>Auxiliar</option>
                                            <option value="visitante" {{ $tipoAtual === 'visitante' ? 'selected' : '' }}>Visitante</option>
                                            <option value="ex-integrante" {{ $tipoAtual === 'ex-integrante' ? 'selected' : '' }}>Ex-Integrante</option>
                                        </select>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6 flex justify-end">
                    <x-primary-button>
                        {{ __('Salvar Alterações') }}
                    </x-primary-button>
                </div>
            </form>
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
