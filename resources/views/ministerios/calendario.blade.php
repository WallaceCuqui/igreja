<x-layouts.app title="Agenda Geral da Igreja">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            📅 Agenda Geral dos Ministérios
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div id="calendar"
             class="bg-white p-6 rounded-lg shadow"
             data-eventos='@json($eventosJson)'>
        </div>
    </div>

    {{-- ✅ Modal para detalhes do evento --}}
    <div id="eventoModal"
         class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6 relative">
            <button id="fecharModal"
                    class="absolute top-2 right-3 text-gray-500 hover:text-gray-700 text-xl">
                &times;
            </button>
            <h3 id="modalTitulo" class="text-lg font-semibold text-indigo-700 mb-3"></h3>
            <p id="modalDescricao" class="text-gray-700 whitespace-pre-line text-sm"></p>
        </div>
    </div>

    @vite(['resources/js/calendario.js'])
</x-layouts.app>
