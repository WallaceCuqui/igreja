<x-layouts.app title="Lideranças">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Lideranças dos Ministérios') }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        {{-- ✅ FORMULÁRIO DE CADASTRO / EDIÇÃO --}}
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">
                {{ isset($editando) ? 'Editar Liderança' : 'Cadastrar Nova Liderança' }}
            </h3>

            <form method="POST"
                action="{{ isset($editando)
                    ? route('ministerios.liderancas.update', [$ministerio->id, $editando->id])
                    : route('ministerios.liderancas.store', $ministerio->id) }}">


                @csrf

                <input type="hidden" name="ministerio_id" value="{{ $ministerio->id }}">

                @if(isset($editando))
                    @method('PUT')
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">

                    @php
                    $nomeLider = null;
                    if($editando && $editando->lider_id) {
                        $lider = \App\Models\User::find($editando->lider_id);
                        $nomeLider = $lider?->name;
                    }
                    @endphp

                    <div class="relative">
                        <x-input-label value="Buscar Líder" />
                        
                        <input type="text"
                            class="busca-dinamica border-gray-300 rounded-md w-full"
                            placeholder="Digite o nome do líder..."
                            data-target-input="#lider_id"
                            data-endpoint="{{ route('membro.busca') }}"
                            data-results=".resultados-dinamicos"
                            value="{{ old('lider_id', $nomeLider) }}"> <!-- preenche o campo -->

                        <input type="hidden" id="lider_id" name="lider_id" value="{{ old('lider_id', $editando->lider_id ?? '') }}">

                        <div class="resultados-dinamicos absolute bg-white border border-gray-300 rounded-md w-full mt-1 max-h-40 overflow-y-auto shadow-lg z-10"></div>
                        <x-input-error :messages="$errors->get('lider_id')" />
                    </div>


                    <div>
                        <x-input-label for="vice_id" value="Vice-líder (opcional)" />
                        <select name="vice_id" id="vice_id" class="w-full border-gray-300 rounded-md mt-1">
                            <option value="">Nenhum</option>
                            @foreach($usuarios as $u)
                                <option value="{{ $u->id }}"
                                    {{ old('vice_id', $editando->vice_id ?? '') == $u->id ? 'selected' : '' }}>
                                    {{ $u->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <x-input-label for="data_inicio" value="Data de Início" />
                        <x-text-input id="data_inicio" name="data_inicio" type="date"
                            value="{{ old('data_inicio', $editando->data_inicio ?? '') }}" class="w-full mt-1" />
                    </div>

                    <div>
                        <x-input-label for="data_fim" value="Data de Fim" />
                        <x-text-input id="data_fim" name="data_fim" type="date"
                            value="{{ old('data_fim', $editando->data_fim ?? '') }}" class="w-full mt-1" />
                    </div>

                    <div class="mb-4 flex items-center">
                        <input type="checkbox" name="ativo" id="ativo" value="1"
                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                            @checked(old('ativo', $lideranca->ativo ?? false))>
                        <label for="ativo" class="ms-2 text-sm text-gray-700">Ativo</label>
                    </div>

                </div>

                <div class="flex items-center gap-2">
                    <x-primary-button>
                        {{ isset($editando) ? 'Salvar Alterações' : 'Cadastrar' }}
                    </x-primary-button>

                    @if(isset($editando))
                        <a href="{{ route('ministerios.liderancas.index') }}" class="text-gray-600 hover:underline">Cancelar</a>
                    @endif
                </div>
            </form>
        </div>

        {{-- ✅ LISTA DE LIDERANÇAS --}}
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">
                Lideranças do Ministério: <span class="text-indigo-600">{{ $ministerio->nome }}</span>
            </h3>

            @if($liderancas->isEmpty())
                <p class="text-gray-500">Nenhuma liderança cadastrada para este ministério.</p>
            @else
                <table class="min-w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-100 text-left">
                            <th class="px-4 py-2">Líder</th>
                            <th class="px-4 py-2">Vice</th>
                            <th class="px-4 py-2">Período</th>
                            <th class="px-4 py-2">Status</th>
                            <th class="px-4 py-2 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($liderancas as $lideranca)
                            <tr class="border-t">
                                <td class="px-4 py-2">{{ $lideranca->lider->name }}</td>
                                <td class="px-4 py-2">{{ $lideranca->vice->name ?? '-' }}</td>
                                <td class="px-4 py-2">
                                    {{ \Carbon\Carbon::parse($lideranca->data_inicio)->format('d/m/Y') }}
                                    –
                                    {{ $lideranca->data_fim ? \Carbon\Carbon::parse($lideranca->data_fim)->format('d/m/Y') : 'Atualmente' }}
                                </td>
                                <td class="px-4 py-2">
                                    @if($lideranca->ativo)
                                        <span class="text-green-600 font-semibold">Ativo</span>
                                    @else
                                        <span class="text-red-500 font-semibold">Inativo</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-right space-x-2">
                                    <a href="{{ route('ministerios.liderancas.index', [$ministerio->id, 'edit' => $lideranca->id]) }}">
                                        <x-secondary-button>Editar</x-secondary-button>
                                    </a>
                                    <form method="POST"
                                        action="{{ route('ministerios.liderancas.destroy', [$ministerio->id, $lideranca->id]) }}"
                                        class="inline"
                                        onsubmit="return confirm('Tem certeza que deseja excluir esta liderança?')">
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

</x-app-layout>
