<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
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
            <x-input-label for="name" :value="__('Nome')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <!-- Email (somente leitura) -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full bg-gray-100 cursor-not-allowed"
                :value="old('email', $user->email)" required autocomplete="username" readonly />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        <!-- Nome Fantasia -->
        <div>
            <x-input-label for="nome_fantasia" :value="__('Nome Fantasia')" />
            <x-text-input id="nome_fantasia" name="nome_fantasia" type="text" class="mt-1 block w-full"
                :value="old('nome_fantasia', $user->detalhesUsuario->nome_fantasia ?? '')" />
            <x-input-error class="mt-2" :messages="$errors->get('nome_fantasia')" />
        </div>

        <!-- Documento (CPF/CNPJ) -->
        <div>
            <x-input-label for="documento" :value="__('CPF/CNPJ')" />
            <x-text-input id="documento" name="documento" type="text" class="mt-1 block w-full"
                :value="old('documento', $user->detalhesUsuario->documento ?? '')" />
            <x-input-error class="mt-2" :messages="$errors->get('documento')" />
        </div>

        <!-- Gênero -->
        <div>
            <x-input-label for="genero" :value="__('Gênero')" />
            <select id="genero" name="genero" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                <option value="">Selecione</option>
                <option value="Masculino" {{ old('genero', $user->detalhesUsuario->genero ?? '') === 'Masculino' ? 'selected' : '' }}>Masculino</option>
                <option value="Feminino" {{ old('genero', $user->detalhesUsuario->genero ?? '') === 'Feminino' ? 'selected' : '' }}>Feminino</option>
                <option value="Outro" {{ old('genero', $user->detalhesUsuario->genero ?? '') === 'Outro' ? 'selected' : '' }}>Outro</option>
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('genero')" />
        </div>

        <!-- Data de nascimento -->
        <div>
            <x-input-label for="data_nascimento" :value="__('Data de Nascimento')" />
            <x-text-input id="data_nascimento" name="data_nascimento" type="date" class="mt-1 block w-full"
                :value="old('data_nascimento', $user->detalhesUsuario->data_nascimento ?? '')" />
            <x-input-error class="mt-2" :messages="$errors->get('data_nascimento')" />
        </div>

        <!-- Telefone -->
        <div>
            <x-input-label for="telefone" :value="__('Telefone')" />
            <x-text-input id="telefone" name="telefone" type="text" class="mt-1 block w-full"
                :value="old('telefone', $user->detalhesUsuario->telefone ?? '')" />
            <x-input-error class="mt-2" :messages="$errors->get('telefone')" />
        </div>

        <!-- CEP -->
        <div>
            <x-input-label for="cep" :value="__('CEP')" />
            <x-text-input id="cep" name="cep" type="text" class="mt-1 block w-full"
                :value="old('cep', $user->detalhesUsuario->cep ?? '')"
                placeholder="00000-000" maxlength="9" />
            <p id="cep-status" class="text-sm text-gray-500 mt-1"></p>
            <x-input-error class="mt-2" :messages="$errors->get('cep')" />
        </div>

        <!-- Endereço -->
        <div>
            <x-input-label for="endereco" :value="__('Endereço')" />
            <x-text-input id="endereco" name="endereco" type="text" class="mt-1 block w-full"
                :value="old('endereco', $user->detalhesUsuario->endereco ?? '')" />
        </div>

        <!-- Número -->
        <div>
            <x-input-label for="numero" :value="__('Número')" />
            <x-text-input id="numero" name="numero" type="text" class="mt-1 block w-full"
                :value="old('numero', $user->detalhesUsuario->numero ?? '')" />
            <x-input-error class="mt-2" :messages="$errors->get('numero')" />
        </div>

        <!-- Complemento -->
        <div>
            <x-input-label for="complemento" :value="__('Complemento')" />
            <x-text-input id="complemento" name="complemento" type="text" class="mt-1 block w-full"
                :value="old('complemento', $user->detalhesUsuario->complemento ?? '')" />
            <x-input-error class="mt-2" :messages="$errors->get('complemento')" />
        </div>

        <!-- Bairro -->
        <div>
            <x-input-label for="bairro" :value="__('Bairro')" />
            <x-text-input id="bairro" name="bairro" type="text" class="mt-1 block w-full"
                :value="old('bairro', $user->detalhesUsuario->bairro ?? '')" />
        </div>

        <!-- Cidade -->
        <div>
            <x-input-label for="cidade" :value="__('Cidade')" />
            <x-text-input id="cidade" name="cidade" type="text" class="mt-1 block w-full"
                :value="old('cidade', $user->detalhesUsuario->cidade ?? '')" />
        </div>

        <!-- Estado -->
        <div>
            <x-input-label for="estado" :value="__('Estado')" />
            <x-text-input id="estado" name="estado" type="text" class="mt-1 block w-full"
                :value="old('estado', $user->detalhesUsuario->estado ?? '')" maxlength="2" />
        </div>

        

        <!-- Botão salvar -->
        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600">
                    {{ __('Saved.') }}
                </p>
            @endif
        </div>
    </form>

    <script src="{{ asset('js/buscaCEP.js') }}"></script>

</section>
