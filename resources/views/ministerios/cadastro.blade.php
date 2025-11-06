<x-layouts.app title="Ministérios">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ministérios') }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        {{-- ✅ FORMULÁRIO DE CADASTRO / EDIÇÃO --}}
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">
                {{ isset($editando) ? 'Editar Ministério' : 'Cadastrar Novo Ministério' }}
            </h3>

            <form method="POST"
                action="{{ isset($editando) ? route('ministerios.update', $editando) : route('ministerios.store') }}">
                @csrf
                @if(isset($editando))
                    @method('PUT')
                @endif

                <div class="mb-4">
                    <x-input-label for="nome" value="Nome do Ministério" />
                    <x-text-input id="nome" name="nome" type="text"
                        value="{{ old('nome', $editando->nome ?? '') }}" class="block w-full mt-1" required />
                </div>

                <div class="mb-4">
                    <x-input-label for="descricao" value="Descrição" />
                    <textarea name="descricao" id="descricao" rows="3"
                        class="w-full border-gray-300 rounded-md shadow-sm mt-1">{{ old('descricao', $editando->descricao ?? '') }}</textarea>
                </div>

                <div class="mb-4">
                    <x-input-label for="data_fundacao" value="Data de Fundação" />
                    <x-text-input id="data_fundacao" name="data_fundacao" type="date"
                        value="{{ old('data_fundacao', $editando->data_fundacao ?? '') }}" class="block w-full mt-1" />
                </div>

                <div class="mb-4 flex items-center">
                    <input type="checkbox" id="ativo" name="ativo"
                        {{ old('ativo', $editando->ativo ?? true) ? 'checked' : '' }} class="mr-2">
                    <x-input-label for="ativo" value="Ativo" />
                </div>

                <div class="flex items-center gap-2">
                    <x-primary-button>
                        {{ isset($editando) ? 'Salvar Alterações' : 'Cadastrar' }}
                    </x-primary-button>

                    @if(isset($editando))
                        <a href="{{ route('ministerios.index') }}"
                            class="text-gray-600 hover:underline">Cancelar</a>
                    @endif
                </div>
            </form>
        </div>

        {{-- ✅ LISTA DE MINISTÉRIOS --}}
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">Lista de Ministérios</h3>

            <table class="min-w-full border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-left">
                        <th class="px-4 py-2">Nome</th>
                        <th class="px-4 py-2">Descrição</th>
                        <th class="px-4 py-2">Ativo</th>
                        <th class="px-4 py-2 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ministerios as $ministerio)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $ministerio->nome }}</td>
                            <td class="px-4 py-2">{{ $ministerio->descricao }}</td>
                            <td class="px-4 py-2">
                                {{ $ministerio->ativo ? 'Sim' : 'Não' }}
                            </td>
                            <td class="px-4 py-2 text-right space-x-2">
                                <a href="{{ route('ministerios.index', ['edit' => $ministerio->id]) }}">
                                    <x-secondary-button>Editar</x-secondary-button>
                                </a>

                                <form method="POST"
                                    action="{{ route('ministerios.destroy', $ministerio) }}"
                                    class="inline"
                                    onsubmit="return confirm('Tem certeza que deseja excluir este ministério?')">
                                    @csrf
                                    @method('DELETE')
                                    <x-danger-button>Excluir</x-danger-button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-4 text-center text-gray-500">
                                Nenhum ministério cadastrado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
