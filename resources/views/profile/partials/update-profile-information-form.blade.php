<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            Informações do Perfil
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            Atualize as informações da sua conta.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <!-- Nome -->
        <div>
            <x-input-label for="name" :value="'Nome Completo / Razão Social'" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <!-- Email (somente leitura) -->
        <div>
            <x-input-label for="email" :value="'E-mail'" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full bg-gray-100 cursor-not-allowed"
                :value="old('email', $user->email)" required autocomplete="username" readonly />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        <h3 class="text-lg font-medium text-gray-900">
            Complementos do Perfil
        </h3>

        <!-- Foto do Perfil -->
        <div>
            <x-input-label for="foto" :value="'Foto do Perfil'" />

            <div class="flex items-center gap-4 mt-2">
                @if ($user->detalhesUsuario && $user->detalhesUsuario->foto)
                    <img src="{{ asset('storage/' . $user->detalhesUsuario->foto) }}"
                        alt="Foto de perfil"
                        class="w-24 h-24 rounded-full object-cover">
                @else
                    <!-- Ícone padrão quando não há foto -->
                    <div class="w-9 h-9 rounded-full bg-gray-200 flex items-center justify-center border border-gray-300 hover:ring-2 hover:ring-indigo-400 transition duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 7.5a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a8.25 8.25 0 0115 0" />
                         </svg>
                    </div>
                @endif

                <input id="foto" name="foto" type="file"
                    class="block w-full text-sm text-gray-500 
                            file:mr-4 file:py-2 file:px-4
                            file:rounded-md file:border-0
                            file:text-sm file:font-semibold
                            file:bg-indigo-50 file:text-indigo-700
                            hover:file:bg-indigo-100"
                    accept="image/*">
            </div>
            @if ($user->detalhesUsuario && $user->detalhesUsuario->foto)
                <button type="button"
                        onclick="document.getElementById('remover-foto').value = 1; this.closest('form').submit();"
                        class="mt-2 text-red-600 text-sm hover:underline">
                    Remover foto
                </button>
            @endif

            <input type="hidden" id="remover-foto" name="remover_foto" value="0">

            <x-input-error class="mt-2" :messages="$errors->get('foto')" />
        </div>

        <!-- Documento (CPF/CNPJ) -->
        <div>
            <x-input-label for="documento" :value="'CPF/CNPJ'" />
            <x-text-input id="documento" name="documento" type="text" class="mt-1 block w-full"
                :value="old('documento', $user->detalhesUsuario->documento ?? '')" />
            <x-input-error class="mt-2" :messages="$errors->get('documento')" />
        </div>

        <div class="mt-2 flex items-center">
            <input id="sem_cnpj" name="sem_cnpj" type="checkbox" value="1"
                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
            <label for="sem_cnpj" class="ml-2 text-sm text-gray-700">
                A igreja não possui CNPJ
            </label>
        </div>


        <!-- Nome Fantasia -->
        <div id="campo-nome-fantasia" style="display: none;">
            <x-input-label for="nome_fantasia" :value="'Nome Fantasia'" />
            <x-text-input id="nome_fantasia" name="nome_fantasia" type="text" class="mt-1 block w-full"
                :value="old('nome_fantasia', $user->detalhesUsuario->nome_fantasia ?? '')" />
            <x-input-error class="mt-2" :messages="$errors->get('nome_fantasia')" />
        </div>

        <!-- Gênero -->
        <div id="campo-genero">
            <x-input-label for="genero" :value="'Gênero'" />
            <select id="genero" name="genero" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                <option value="">Selecione</option>
                <option value="Masculino" {{ old('genero', $user->detalhesUsuario->genero ?? '') === 'Masculino' ? 'selected' : '' }}>Masculino</option>
                <option value="Feminino" {{ old('genero', $user->detalhesUsuario->genero ?? '') === 'Feminino' ? 'selected' : '' }}>Feminino</option>
                <option value="Outro" {{ old('genero', $user->detalhesUsuario->genero ?? '') === 'Outro' ? 'selected' : '' }}>Outro</option>
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('genero')" />
        </div>

        <!-- Data de nascimento -->
        <div id="campo-data-nascimento">
            <x-input-label for="data_nascimento" :value="'Data de Nascimento'" />
            <x-text-input id="data_nascimento" name="data_nascimento" type="date" class="mt-1 block w-full"
                :value="old('data_nascimento', $user->detalhesUsuario->data_nascimento ?? '')" />
            <x-input-error class="mt-2" :messages="$errors->get('data_nascimento')" />
        </div>

        <!-- Telefone -->
        <div>
            <x-input-label for="telefone" :value="'Telefone'" />
            <x-text-input id="telefone" name="telefone" type="text" class="mt-1 block w-full"
                :value="old('telefone', $user->detalhesUsuario->telefone ?? '')" placeholder="(00) 00000-0000" />
            <x-input-error class="mt-2" :messages="$errors->get('telefone')" />
        </div>

        <!-- Igreja (vinculação) -->
        @php
            $igrejaUsuario = $user->igreja; // relação do user com a igreja
        @endphp

        <div id="campo-igreja" class="mt-4">
            <x-input-label for="igreja_busca" value="Igreja" />
            <input 
                id="igreja_busca" 
                type="text" 
                placeholder="Digite o nome da igreja"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                value="{{ old('igreja_busca', $igrejaUsuario->detalhesUsuario->nome_fantasia ?? $igrejaUsuario->name ?? '') }}"
            >
            <input 
                id="igreja_id" 
                type="hidden" 
                name="igreja_id" 
                value="{{ old('igreja_id', $user->igreja_id ?? '') }}"
            >
            <ul id="lista-igrejas" class="border rounded mt-1 hidden bg-white max-h-60 overflow-auto"></ul>
        </div>





        <!-- CEP -->
        <div>
            <x-input-label for="cep" :value="'CEP'" />
            <x-text-input id="cep" name="cep" type="text" class="mt-1 block w-full"
                :value="old('cep', $user->detalhesUsuario->cep ?? '')"
                placeholder="00000-000" maxlength="9" />
            <p id="cep-status" class="text-sm text-gray-500 mt-1"></p>
            <x-input-error class="mt-2" :messages="$errors->get('cep')" />
        </div>

        <!-- Endereço -->
        <div>
            <x-input-label for="endereco" :value="'Endereço'" />
            <x-text-input id="endereco" name="endereco" type="text" class="mt-1 block w-full"
                :value="old('endereco', $user->detalhesUsuario->endereco ?? '')" />
        </div>

        <!-- Número -->
        <div>
            <x-input-label for="numero" :value="'Número'" />
            <x-text-input id="numero" name="numero" type="text" class="mt-1 block w-full"
                :value="old('numero', $user->detalhesUsuario->numero ?? '')" />
            <x-input-error class="mt-2" :messages="$errors->get('numero')" />
        </div>

        <!-- Complemento -->
        <div>
            <x-input-label for="complemento" :value="'Complemento'" />
            <x-text-input id="complemento" name="complemento" type="text" class="mt-1 block w-full"
                :value="old('complemento', $user->detalhesUsuario->complemento ?? '')" />
            <x-input-error class="mt-2" :messages="$errors->get('complemento')" />
        </div>

        <!-- Bairro -->
        <div>
            <x-input-label for="bairro" :value="'Bairro'" />
            <x-text-input id="bairro" name="bairro" type="text" class="mt-1 block w-full"
                :value="old('bairro', $user->detalhesUsuario->bairro ?? '')" />
        </div>

        <!-- Cidade -->
        <div>
            <x-input-label for="cidade" :value="'Cidade'" />
            <x-text-input id="cidade" name="cidade" type="text" class="mt-1 block w-full"
                :value="old('cidade', $user->detalhesUsuario->cidade ?? '')" />
        </div>

        <!-- Estado -->
        <div>
            <x-input-label for="estado" :value="'Estado'" />
            <x-text-input id="estado" name="estado" type="text" class="mt-1 block w-full"
                :value="old('estado', $user->detalhesUsuario->estado ?? '')" maxlength="2" />
        </div>

        
        <!-- Botão salvar -->
        <div class="flex items-center gap-4">
            <x-primary-button>Salvar</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600">
                    Salvo com sucesso.
                </p>
            @endif
        </div>
    </form>

    <!-- Máscara de input -->
    <script src="https://unpkg.com/imask"></script>

</section>
