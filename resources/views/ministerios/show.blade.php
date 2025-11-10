<!-- resources/views/ministerios/show.blade.php -->
<x-layouts.app :title="$ministerio->nome">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $ministerio->nome }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-6">

            {{-- 📋 Descrição e informações básicas --}}
            <section>
                <p class="text-gray-700 mb-4">{{ $ministerio->descricao ?? 'Sem descrição disponível.' }}</p>

                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Data de Fundação</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $ministerio->data_fundacao?->format('d/m/Y') ?? '—' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $ministerio->ativo ? 'Ativo' : 'Inativo' }}
                        </dd>
                    </div>
                </dl>
            </section>

            {{-- 👥 Liderança --}}
            <section class="border-t pt-4">
                <h3 class="text-lg font-semibold mb-3 text-gray-800">Liderança</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Líder</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $ministerio->lider?->name ?? '—' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Vice-Líder</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $ministerio->vice?->name ?? '—' }}
                        </dd>
                    </div>
                </div>
            </section>

            {{-- 🧑‍🤝‍🧑 Comissões --}}
            <section class="border-t pt-4">
                <h3 class="text-lg font-semibold mb-3 text-gray-800">Comissões</h3>

                @if($ministerio->comissoes->count())
                    <ul class="list-disc list-inside text-sm text-gray-700 space-y-1">
                        @foreach ($ministerio->comissoes as $comissao)
                            <li>
                                <span class="font-semibold">{{ $comissao->membro?->name ?? '—' }}</span>
                                — <span class="text-gray-600">{{ ucfirst($comissao->funcao) }}</span>
                                @if (!$comissao->ativo)
                                    <span class="text-red-500 text-xs">(Inativo)</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-gray-500 text-sm">Nenhuma comissão cadastrada.</p>
                @endif
            </section>

            {{-- ✋ Inscrição no Ministério (visível apenas para membros) --}}
            @if(Auth::check() && Auth::user()->isMembro())
                <section class="border-t pt-4">
                    <h3 class="text-lg font-semibold mb-3 text-gray-800">Participação</h3>

                    @php
                        $integrante = $ministerio->integrantes()
                            ->where('membro_id', Auth::id())
                            ->first();
                    @endphp

                    @if ($integrante)
                        @if ($integrante->pivot->status === 'ativo')
                            <p class="text-green-600 text-sm font-medium">
                                ✅ Você já faz parte deste ministério.
                            </p>
                        @elseif ($integrante->pivot->status === 'pendente')
                            <p class="text-yellow-600 text-sm font-medium">
                                ⏳ Sua solicitação de participação está pendente de aprovação.
                            </p>
                        @elseif ($integrante->pivot->status === 'inativo')
                            <p class="text-gray-500 text-sm font-medium">
                                ⚪ Sua participação neste ministério está inativa no momento.
                            </p>
                        @endif
                    @else
                        <form action="{{ route('ministerios.integrantes.solicitar', $ministerio->id) }}" method="POST">
                            @csrf
                            <x-primary-button>
                                {{ $ministerio->politica_ingresso === 'restrito' ? 'Solicitar participação' : 'Inscrever-se' }}
                            </x-primary-button>
                        </form>
                    @endif
                </section>
            @endif





            @if(Auth::check() && Auth::user()->isIgreja())
                {{-- 🔗 Atalhos --}}
                <section class="border-t pt-4">
                    <h3 class="text-lg font-semibold mb-2">Atalhos</h3>
                    <div class="space-y-1">
                        <x-dropdown-link :href="route('ministerios.agendas.index', $ministerio->id)">📅 Agenda</x-dropdown-link>
                        <x-dropdown-link :href="route('ministerios.comissoes.index', $ministerio->id)">🧩 Comissões</x-dropdown-link>
                        <x-dropdown-link :href="route('ministerios.integrantes.index', $ministerio->id)">👥 Integrantes</x-dropdown-link>
                        <x-dropdown-link :href="route('ministerios.liderancas.index', $ministerio->id)">🏅 Lideranças</x-dropdown-link>
                    </div>
                </section>
            @endif
        </div>
    </div>
</x-layouts.app>

