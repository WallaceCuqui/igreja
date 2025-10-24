<x-layouts.app title="Abrir Protocolo">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Caixa de instrução -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    {{ __("Preencha o formulário abaixo para abrir um novo protocolo.") }}
                </div>
            </div>

            <!-- Mensagem de sucesso -->
            @if (session('success'))
                <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Formulário -->
            <div class="bg-white p-6 rounded shadow">
                <form method="POST" action="{{ route('protocolo.store') }}">
                    @csrf

                    <div class="mb-4">
                        <label class="block font-semibold">Nome</label>
                        <input type="text" name="nome" value="{{ old('nome') }}" class="border p-2 w-full rounded">
                        @error('nome') <p class="text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-semibold">E-mail</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="border p-2 w-full rounded">
                        @error('email') <p class="text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-semibold">Assunto</label>
                        <input type="text" name="assunto" value="{{ old('assunto') }}" class="border p-2 w-full rounded">
                        @error('assunto') <p class="text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-semibold">Mensagem</label>
                        <textarea name="mensagem" rows="5" class="border p-2 w-full rounded">{{ old('mensagem') }}</textarea>
                        @error('mensagem') <p class="text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                        Enviar
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
