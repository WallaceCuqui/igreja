<x-layouts.app title="Perfil do Membro">
    <section class="max-w-4xl mx-auto py-10">
        
        <header>
            <h2 class="text-2xl font-semibold text-gray-900">
                Perfil do Membro
            </h2>
            <p class="mt-1 text-sm text-gray-600">
                Informações completas do membro selecionado.
            </p>
        </header>

        <div class="mt-8 bg-white shadow-sm rounded-lg p-6 space-y-8">

            {{-- FOTO + NOME --}}
            <div class="flex items-center gap-6">
                @if ($membro->detalhesUsuario?->foto)
                    <img src="{{ asset('storage/' . $membro->detalhesUsuario->foto) }}"
                         class="w-28 h-28 rounded-full object-cover">
                @else
                    <div class="w-28 h-28 rounded-full bg-gray-200 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M15.75 7.5a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a8.25 8.25 0 0115 0" />
                        </svg>
                    </div>
                @endif

                <div>
                    <h3 class="text-xl font-bold text-gray-900">{{ $membro->name }}</h3>
                    <p class="text-gray-600 text-sm">{{ $membro->email }}</p>

                    @if($membro->igreja)
                        <p class="mt-1 text-sm text-indigo-700 font-medium">
                            Igreja: {{ $membro->igreja->name }}
                        </p>
                    @endif
                </div>
            </div>

            {{-- DADOS PESSOAIS --}}
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-3">Dados Pessoais</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <div>
                        <p class="text-sm font-semibold text-gray-700">Gênero</p>
                        <p class="text-gray-900">
                            {{ $membro->detalhesUsuario->genero ?? 'Não informado' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm font-semibold text-gray-700">Data de Nascimento</p>
                        <p class="text-gray-900">
                            @if ($membro->detalhesUsuario?->data_nascimento)
                                {{ date('d/m/Y', strtotime($membro->detalhesUsuario->data_nascimento)) }}
                            @else
                                Não informado
                            @endif
                        </p>
                    </div>

                    <div>
                        <p class="text-sm font-semibold text-gray-700">CPF/CNPJ</p>
                        <p class="text-gray-900">{{ $membro->detalhesUsuario->documento_mascarado ?? 'Não informado' }}</p>
                    </div>

                    <div>
                        <p class="text-sm font-semibold text-gray-700">Telefone</p>
                        <p class="text-gray-900">{{ $membro->detalhesUsuario->telefone ?? 'Não informado' }}</p>
                    </div>

                </div>
            </div>

            {{-- ENDEREÇO --}}
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-3">Endereço</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <div>
                        <p class="text-sm font-semibold text-gray-700">CEP</p>
                        <p class="text-gray-900">{{ $membro->detalhesUsuario->cep ?? 'Não informado' }}</p>
                    </div>

                    <div>
                        <p class="text-sm font-semibold text-gray-700">Endereço</p>
                        <p class="text-gray-900">{{ $membro->detalhesUsuario->endereco ?? 'Não informado' }}</p>
                    </div>

                    <div>
                        <p class="text-sm font-semibold text-gray-700">Número</p>
                        <p class="text-gray-900">{{ $membro->detalhesUsuario->numero ?? 'Não informado' }}</p>
                    </div>

                    <div>
                        <p class="text-sm font-semibold text-gray-700">Complemento</p>
                        <p class="text-gray-900">{{ $membro->detalhesUsuario->complemento ?? 'Não informado' }}</p>
                    </div>

                    <div>
                        <p class="text-sm font-semibold text-gray-700">Bairro</p>
                        <p class="text-gray-900">{{ $membro->detalhesUsuario->bairro ?? 'Não informado' }}</p>
                    </div>

                    <div>
                        <p class="text-sm font-semibold text-gray-700">Cidade / Estado</p>
                        <p class="text-gray-900">
                            {{ $membro->detalhesUsuario->cidade ?? 'Não informado' }} /
                            {{ $membro->detalhesUsuario->estado ?? 'Não informado' }}
                        </p>
                    </div>

                </div>
            </div>

            {{-- RELAÇÕES (dependentes sem login) --}}
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-3">Relações (Dependentes)</h3>

                @if ($membro->relacoes->isEmpty())
                    <p class="text-gray-600">Nenhum dependente cadastrado.</p>
                @else
                    <ul class="space-y-3">
                        @foreach ($membro->relacoes as $r)
                            <li class="flex items-center gap-3">
                                {{-- Foto --}}
                                @if ($r->foto)
                                    <img src="{{ asset('storage/' . $r->foto) }}" class="w-12 h-12 rounded-full object-cover">
                                @else
                                    <div class="w-12 h-12 bg-gray-200 rounded-full"></div>
                                @endif

                                <div>
                                    <p class="font-semibold text-gray-900">{{ $r->nome }}</p>
                                    <p class="text-sm text-gray-600">
                                        {{ $r->tipo ?? 'Relação' }}
                                    </p>

                                    @if ($r->data_nascimento)
                                        <p class="text-xs text-gray-500">
                                            Nasc.: {{ date('d/m/Y', strtotime($r->data_nascimento)) }}
                                        </p>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>


            {{-- MINISTÉRIOS --}}
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-3">Participa dos Ministérios</h3>

                @if ($membro->ministerios->isEmpty())
                    <p class="text-gray-600">Este membro não participa de nenhum ministério.</p>
                @else
                    <ul class="list-disc pl-6 text-gray-800">
                        @foreach ($membro->ministerios as $min)
                            <li>
                                <strong>{{ $min->nome }}</strong> — 
                                <span class="text-gray-600">
                                    {{ $min->pivot->status }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            {{-- COMISSÃO --}}
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-3">Comissão</h3>

                @if ($membro->comissoes->isEmpty())
                    <p class="text-gray-600">Não participa da comissão de nenhum ministério.</p>
                @else
                    <ul class="list-disc pl-6 text-gray-800">
                        @foreach ($membro->comissoes as $c)
                            <li>{{ $c->ministerio->nome }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>

            {{-- LIDERANÇAS --}}
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-3">Lideranças</h3>

                @php
                    $liderancas = collect([]);

                    $liderancas = $liderancas
                        ->merge($membro->liderandoComoLider ?? [])
                        ->merge($membro->liderandoComoVice ?? []);
                @endphp

                @if ($liderancas->isEmpty())
                    <p class="text-gray-600">Este membro não exerce liderança.</p>
                @else
                    <ul class="list-disc pl-6 text-gray-800">
                        @foreach ($liderancas as $l)
                            <li>
                                {{ $l->ministerio->nome }} —
                                <strong>
                                    @if ($l->lider_id == $membro->id)
                                        Líder
                                    @else
                                        Vice-Líder
                                    @endif
                                </strong>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

        </div>

    </section>
</x-app-layout>
