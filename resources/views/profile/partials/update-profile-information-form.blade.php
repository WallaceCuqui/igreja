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

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
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

        <!-- Documento (CPF/CNPJ) -->
        <div>
            <x-input-label for="documento" :value="'CPF/CNPJ'" />
            <x-text-input id="documento" name="documento" type="text" class="mt-1 block w-full"
                :value="old('documento', $user->detalhesUsuario->documento ?? '')" />
            <x-input-error class="mt-2" :messages="$errors->get('documento')" />
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

    <!-- Scripts para validar documentos e buscar endereço pelo CEP -->
    <script src="{{ asset('js/validaDocumento.js') }}"></script>
    <script src="{{ asset('js/buscaCEP.js') }}"></script>
    <script src="https://unpkg.com/imask"></script>
    <script src="{{ asset('js/mascaras.js') }}"></script>
</section>
