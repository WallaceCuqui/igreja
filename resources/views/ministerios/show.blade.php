<!-- resources/views/ministerios/show.blade.php -->
<x-layouts.app :title="$ministerio->nome">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $ministerio->nome }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm sm:rounded-lg p-6">
            <p class="text-gray-700 mb-4">{{ $ministerio->descricao ?? 'Sem descrição' }}</p>

            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Data de Fundação</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $ministerio->data_fundacao?->format('d/m/Y') ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $ministerio->ativo ? 'Ativo' : 'Inativo' }}
                    </dd>
                </div>
            </dl>

            <div class="border-t pt-4">
                <h3 class="text-lg font-semibold mb-2">Atalhos</h3>
                <div class="space-x-2">
                    <x-dropdown-link :href="route('ministerios.integrantes.index', $ministerio->id)">Integrantes</x-dropdown-link>
                    <x-dropdown-link :href="route('ministerios.liderancas.index', $ministerio->id)">Lideranças</x-dropdown-link>
                    <x-dropdown-link :href="route('ministerios.comissoes.index', $ministerio->id)">Comissões</x-dropdown-link>
                    <x-dropdown-link :href="route('ministerios.agendas.index', $ministerio->id)">Agenda</x-dropdown-link>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
