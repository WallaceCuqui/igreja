<x-layouts.app title="Relações / Dependentes">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Relações / Dependentes') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Lista de relações existentes -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Relações</h3>

                @if($user->relacoes->isEmpty())
                    <p class="text-gray-600">Nenhuma relação cadastrada ainda.</p>
                @else
                    <ul class="space-y-2">
                        @foreach($user->relacoes as $relacao)
                            <li class="border rounded-md p-2 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    {{-- Foto da relação --}}
                                    @if ($relacao->foto)
                                        <img src="{{ asset('storage/' . $relacao->foto) }}" 
                                            alt="{{ $relacao->nome }}" 
                                            class="w-12 h-12 rounded-full object-cover border border-gray-300">
                                    @else
                                        <img src="{{ asset('images/default-avatar.png') }}" 
                                            alt="Sem foto" 
                                            class="w-12 h-12 rounded-full object-cover border border-gray-300">
                                    @endif

                                    {{-- Informações principais --}}
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $relacao->nome }}</p>
                                        <p class="text-sm text-gray-500">
                                            Nascimento: {{ $relacao->data_nascimento ? \Carbon\Carbon::parse($relacao->data_nascimento)->format('d/m/Y') : '-' }} |
                                            Sexo: {{ $relacao->sexo === 'M' ? 'Masculino' : ($relacao->sexo === 'F' ? 'Feminino' : '-') }}
                                        </p>
                                        <p class="text-xs text-gray-400">Relação: {{ ucfirst($relacao->tipo ?? '-') }}</p>
                                    </div>
                                </div>

                                {{-- Ações --}}
                                <div class="flex gap-2">
                                    <a href="{{ route('profile.relacoes', ['edit' => $relacao->id]) }}" 
                                    class="text-blue-600 hover:underline text-sm">Editar</a>
                                    <form method="POST" 
                                        action="{{ route('profile.relacoes.destroy', $relacao) }}" 
                                        onsubmit="return confirm('Tem certeza que deseja remover esta relação?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline text-sm">Remover</button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <!-- Formulário de cadastro/edição -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                @php
                    $isEdit = isset($editRelacao); // se tiver editRelacao, é edição
                @endphp

                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    {{ $isEdit ? 'Editar Relação' : 'Adicionar Nova Relação' }}
                </h3>

                <form method="POST" 
                      action="{{ $isEdit ? route('profile.relacoes.update', $editRelacao) : route('profile.relacoes.store') }}" 
                      enctype="multipart/form-data">
                    @csrf
                    @if($isEdit)
                        @method('PATCH')
                    @endif


                    <input type="hidden" name="membro_id" value="{{ $user->id }}">

                    <!-- Nome -->
                    <div class="mt-4">
                        <x-input-label for="nome" :value="'Nome'" />
                        <x-text-input id="nome" name="nome" type="text" class="mt-1 block w-full"
                            :value="old('nome', $isEdit ? $editRelacao->nome : '')" required />
                        <x-input-error class="mt-2" :messages="$errors->get('nome')" />
                    </div>

                    <!-- Data de nascimento -->
                    <div class="mt-4">
                        <x-input-label for="data_nascimento" :value="'Data de Nascimento'" />
                        <x-text-input id="data_nascimento" name="data_nascimento" type="date" class="mt-1 block w-full"
                            :value="old('data_nascimento', $isEdit ? $editRelacao->data_nascimento : '')" />
                        <x-input-error class="mt-2" :messages="$errors->get('data_nascimento')" />
                    </div>

                    <!-- Sexo -->
                    <div class="mt-4">
                        <x-input-label for="sexo" :value="'Sexo'" />
                        <select id="sexo" name="sexo" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">Selecione</option>
                            <option value="M" {{ old('sexo', $isEdit ? $editRelacao->sexo : '') === 'M' ? 'selected' : '' }}>Masculino</option>
                            <option value="F" {{ old('sexo', $isEdit ? $editRelacao->sexo : '') === 'F' ? 'selected' : '' }}>Feminino</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('sexo')" />
                    </div>

                    <!-- Foto -->
                    <div class="mt-4 flex items-center gap-4">
                        @if($isEdit && $editRelacao->foto)
                            <img src="{{ asset('storage/' . $editRelacao->foto) }}" class="w-24 h-24 rounded-full object-cover">
                        @endif
                        <input id="foto" name="foto" type="file" accept="image/*">
                    </div>

                    <!-- Tipo de relação -->
                    <div class="mt-4">
                        <x-input-label for="tipo" :value="'Tipo de Relação'" />
                        <select id="tipo" name="tipo" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="">Selecione</option>
                            @foreach($tiposRelacao as $key => $label)
                                <option value="{{ $key }}" {{ old('tipo', $isEdit ? $editRelacao->tipo : '') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Ministério -->
                    <div class="mt-4">
                        <x-input-label for="ministerios" :value="'Ministérios'" />
                        <select id="ministerios" name="ministerios[]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" multiple>
                            @foreach($ministerios as $ministerio)
                                <option value="{{ $ministerio->id }}" 
                                    {{ $isEdit && $editRelacao->ministerios->contains($ministerio->id) ? 'selected' : '' }}>
                                    {{ $ministerio->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mt-6 flex items-center gap-4">
                        <x-primary-button>{{ $isEdit ? 'Salvar Alterações' : 'Adicionar' }}</x-primary-button>
                        <a href="{{ route('profile.relacoes') }}" class="text-gray-600 hover:underline">Cancelar</a>
                    </div>
                </form>
            </div>

            <!-- Vincular adolescente já cadastrado -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Vincular Adolescente (já cadastrado)</h3>

                <form method="POST" action="{{ route('profile.relacoes.vincular') }}">
                    @csrf
                    <input type="hidden" name="membro_id" value="{{ $user->id }}">

                    <div class="mt-4">
                        <x-input-label for="user_id" :value="'Selecione o usuário'" />
                        <select id="user_id" name="user_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="">Selecione</option>
                            @foreach($usuariosAdolescentes as $adolescente)
                                <option value="{{ $adolescente->id }}">{{ $adolescente->name }} ({{ $adolescente->email }})</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('user_id')" />
                    </div>

                    <div class="mt-6 flex items-center gap-4">
                        <x-primary-button>Vincular</x-primary-button>
                        <a href="{{ route('profile.relacoes') }}" class="text-gray-600 hover:underline">Cancelar</a>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-layouts.app>
