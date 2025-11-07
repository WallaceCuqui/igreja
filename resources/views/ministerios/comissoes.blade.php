<x-layouts.app :title="'Comissão do Ministério: ' . $ministerio->nome">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Comissão do Ministério: <span class="text-indigo-600">{{ $ministerio->nome }}</span>
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        {{-- ✅ FORMULÁRIO DE CADASTRO / EDIÇÃO --}}
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">
                {{ $editando ? 'Editar Comissão' : 'Cadastrar Nova Comissão' }}
                : <a href="{{ route('ministerios.show', $ministerio->id) }}" class="text-indigo-600">{{ $ministerio->nome }}</a>
            </h3>

            <form method="POST"
                action="{{ $editando
                    ? route('ministerios.comissoes.update', [$ministerio->id, $editando->id])
                    : route('ministerios.comissoes.store', $ministerio->id) }}">

                @csrf
                @if($editando)
                    @method('PUT')
                @endif

                <input type="hidden" name="ministerio_id" value="{{ $ministerio->id }}">

                @php
                    $membro_nome = null;
                    if ($editando && $editando->membro_id) {
                        $membro = \App\Models\User::find($editando->membro_id);
                        $membro_nome = $membro?->name;
                    }
                @endphp

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div class="relative">
                        <x-input-label value="Buscar Membro" />
                        <input type="text"
                            class="busca-dinamica border-gray-300 rounded-md w-full"
                            placeholder="Digite o nome do membro..."
                            data-target-input="#membro_id"
                            data-endpoint="{{ route('membro.busca') }}"
                            data-results=".resultados-dinamicos"
                            value="{{ old('membro_id', $membro_nome) }}">
                        <input type="hidden" id="membro_id" name="membro_id" value="{{ old('membro_id', $editando->membro_id ?? '') }}">
                        <div class="resultados-dinamicos absolute bg-white border border-gray-300 rounded-md w-full mt-1 max-h-40 overflow-y-auto shadow-lg z-10"></div>
                        <x-input-error :messages="$errors->get('membro_id')" />
                    </div>

                    <div>
                        <x-input-label for="funcao" value="Função" />
                        <x-text-input id="funcao" name="funcao" type="text"
                            value="{{ old('funcao', $editando?->funcao) }}" class="w-full mt-1" />
                        <x-input-error :messages="$errors->get('funcao')" />
                    </div>

                    <div>
                        <x-input-label for="observacoes" value="Observações" />
                        <textarea id="observacoes" name="observacoes" rows="3"
                            class="border-gray-300 rounded-md w-full mt-1">{{ old('observacoes', $editando?->observacoes) }}</textarea>
                        <x-input-error :messages="$errors->get('observacoes')" />
                    </div>

                    <div>
                        <x-input-label for="data_entrada" value="Data de Entrada" />
                        <x-text-input id="data_entrada" name="data_entrada" type="date"
                            value="{{ old('data_entrada', $editando?->data_entrada?->format('Y-m-d')) }}" class="w-full mt-1" />
                        <x-input-error :messages="$errors->get('data_entrada')" />
                    </div>

                    <div>
                        <x-input-label for="data_saida" value="Data de Saída" />
                        <x-text-input id="data_saida" name="data_saida" type="date"
                            value="{{ old('data_saida', $editando?->data_saida?->format('Y-m-d')) }}" class="w-full mt-1" />
                        <x-input-error :messages="$errors->get('data_saida')" />
                    </div>

                    <div class="flex items-center mt-2">
                        <input type="checkbox" name="ativo" id="ativo" value="1"
                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                            @checked(old('ativo', $editando->ativo ?? false))>
                        <label for="ativo" class="ml-2 text-sm text-gray-700">Ativo</label>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <x-primary-button>
                        {{ $editando ? 'Atualizar' : 'Salvar' }}
                    </x-primary-button>

                    @if($editando)
                        <a href="{{ route('ministerios.comissoes.index', $ministerio->id) }}" class="text-gray-600 hover:underline">Cancelar</a>
                    @endif
                </div>
            </form>
        </div>

        {{-- ✅ LISTA DE COMISSÕES --}}
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">
                Comissões Cadastradas:
                <a href="{{ route('ministerios.show', $ministerio->id) }}" class="text-indigo-600">{{ $ministerio->nome }}</a>
            </h3>

            @if($comissoes->isEmpty())
                <p class="text-gray-500">Nenhuma comissão cadastrada para este ministério.</p>
            @else
                <table class="min-w-full text-sm text-left border">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="px-4 py-2">Membro</th>
                            <th class="px-4 py-2">Função</th>
                            <th class="px-4 py-2">Período</th>
                            <th class="px-4 py-2 text-center">Ativo</th>
                            <th class="px-4 py-2 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($comissoes as $comissao)
                            <tr class="border-t hover:bg-gray-50">
                                <td class="px-4 py-2">{{ $comissao->membro->name ?? '—' }}</td>
                                <td class="px-4 py-2">{{ $comissao->funcao }}</td>
                                <td class="px-4 py-2">
                                    {{ $comissao->data_entrada?->format('d/m/Y') ?? '—' }}
                                    @if($comissao->data_saida)
                                        até {{ $comissao->data_saida->format('d/m/Y') }}
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-center">
                                    @if($comissao->ativo)
                                        <span class="text-green-600 font-semibold">Ativo</span>
                                    @else
                                        <span class="text-red-600 font-semibold">Inativo</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-right flex justify-end gap-2">
                                    <a href="{{ route('ministerios.comissoes.index', [$ministerio->id, 'edit' => $comissao->id]) }}">
                                        <x-secondary-button>Editar</x-secondary-button>
                                    </a>
                                    <form method="POST"
                                          action="{{ route('ministerios.comissoes.destroy', [$ministerio->id, $comissao->id]) }}"
                                          onsubmit="return confirm('Tem certeza que deseja excluir esta comissão?')">
                                        @csrf
                                        @method('DELETE')
                                        <x-danger-button>Excluir</x-danger-button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</x-layouts.app>
