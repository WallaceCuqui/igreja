<x-layouts.app :title="'Agenda do Ministério: ' . $ministerio->nome">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Agenda do Ministério: <span class="text-indigo-600">{{ $ministerio->nome }}</span>
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        {{-- ✅ FORMULÁRIO DE CADASTRO / EDIÇÃO DE EVENTO --}}
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">
                {{ $editando ? 'Editar Evento' : 'Cadastrar Novo Evento' }}
                : <a href="{{ route('ministerios.show', $ministerio->id) }}" class="text-indigo-600">{{ $ministerio->nome }}</a>
            </h3>

            <form method="POST"
                action="{{ $editando
                    ? route('ministerios.agendas.update', [$ministerio->id, $editando->id])
                    : route('ministerios.agendas.store', $ministerio->id) }}">

                @csrf
                @if($editando)
                    @method('PUT')
                @endif

                <input type="hidden" name="ministerio_id" value="{{ $ministerio->id }}">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <x-input-label for="titulo" value="Título do Evento" />
                        <x-text-input id="titulo" name="titulo" type="text"
                            value="{{ old('titulo', $editando?->titulo) }}" class="w-full mt-1" required />
                        <x-input-error :messages="$errors->get('titulo')" />
                    </div>

                    <div>
                        <x-input-label for="tipo_evento" value="Tipo de Evento" />
                        <x-text-input id="tipo_evento" name="tipo_evento" type="text"
                            value="{{ old('tipo_evento', $editando?->tipo_evento) }}" 
                            class="w-full mt-1"
                            placeholder="Ex: Ensaio, Reunião, Culto..."  />
                        <x-input-error :messages="$errors->get('tipo_evento')" />
                    </div>

                    <div>
                        <x-input-label for="data_inicio" value="Data e Hora de Início" />
                        <x-text-input id="data_inicio" name="data_inicio" type="datetime-local"
                            value="{{ old('data_inicio', $editando?->data_inicio?->format('Y-m-d\TH:i')) }}"
                            class="w-full mt-1" required />
                        <x-input-error :messages="$errors->get('data_inicio')" />
                    </div>

                    <div>
                        <x-input-label for="data_fim" value="Data e Hora de Término" />
                        <x-text-input id="data_fim" name="data_fim" type="datetime-local"
                            value="{{ old('data_fim', $editando?->data_fim?->format('Y-m-d\TH:i')) }}"
                            class="w-full mt-1" />
                        <x-input-error :messages="$errors->get('data_fim')" />
                    </div>

                    <div>
                        <x-input-label for="local" value="Local" />
                        <x-text-input id="local" name="local" type="text"
                            value="{{ old('local', $editando?->local) }}" class="w-full mt-1" />
                        <x-input-error :messages="$errors->get('local')" />
                    </div>

                    <div>
                        <x-input-label for="status" value="Status" />
                        <select id="status" name="status"
                            class="border-gray-300 rounded-md w-full mt-1 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="planejado" @selected(old('status', $editando?->status) == 'planejado')>Planejado</option>
                            <option value="realizado" @selected(old('status', $editando?->status) == 'realizado')>Realizado</option>
                            <option value="cancelado" @selected(old('status', $editando?->status) == 'cancelado')>Cancelado</option>
                        </select>
                        <x-input-error :messages="$errors->get('status')" />
                    </div>

                    <div class="sm:col-span-2">
                        <x-input-label for="descricao" value="Descrição / Observações" />
                        <textarea id="descricao" name="descricao" rows="3"
                            class="border-gray-300 rounded-md w-full mt-1">{{ old('descricao', $editando?->descricao) }}</textarea>
                        <x-input-error :messages="$errors->get('descricao')" />
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <x-primary-button>
                        {{ $editando ? 'Atualizar' : 'Salvar' }}
                    </x-primary-button>

                    @if($editando)
                        <a href="{{ route('ministerios.agendas.index', $ministerio->id) }}" class="text-gray-600 hover:underline">Cancelar</a>
                    @endif
                </div>
            </form>
        </div>

        {{-- ✅ LISTA DE EVENTOS CADASTRADOS --}}
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">
                Eventos Cadastrados:
                <a href="{{ route('ministerios.show', $ministerio->id) }}" class="text-indigo-600">{{ $ministerio->nome }}</a>
            </h3>

            @if($agendas->isEmpty())
                <p class="text-gray-500">Nenhum evento cadastrado para este ministério.</p>
            @else
                <table class="min-w-full text-sm text-left border">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="px-4 py-2">Título</th>
                            <th class="px-4 py-2">Período</th>
                            <th class="px-4 py-2">Local</th>
                            <th class="px-4 py-2">Tipo</th>
                            <th class="px-4 py-2 text-center">Status</th>
                            <th class="px-4 py-2 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($agendas as $agenda)
                            <tr class="border-t hover:bg-gray-50">
                                <td class="px-4 py-2">{{ $agenda->titulo }}</td>
                                <td class="px-4 py-2">
                                    {{ $agenda->data_inicio?->format('d/m/Y H:i') ?? '—' }}
                                    @if($agenda->data_fim)
                                        até {{ $agenda->data_fim->format('d/m/Y H:i') }}
                                    @endif
                                </td>
                                <td class="px-4 py-2">{{ $agenda->local ?? '—' }}</td>
                                <td class="px-4 py-2">{{ $agenda->tipo_evento ?? '—' }}</td>
                                <td class="px-4 py-2 text-center">
                                    @if($agenda->status === 'planejado')
                                        <span class="text-blue-600 font-semibold">Planejado</span>
                                    @elseif($agenda->status === 'realizado')
                                        <span class="text-green-600 font-semibold">Realizado</span>
                                    @else
                                        <span class="text-red-600 font-semibold">Cancelado</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-right flex justify-end gap-2">
                                    <a href="{{ route('ministerios.agendas.index', [$ministerio->id, 'edit' => $agenda->id]) }}">
                                        <x-secondary-button>Editar</x-secondary-button>
                                    </a>
                                    <form method="POST"
                                          action="{{ route('ministerios.agendas.destroy', [$ministerio->id, $agenda->id]) }}"
                                          onsubmit="return confirm('Tem certeza que deseja excluir este evento?')">
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
