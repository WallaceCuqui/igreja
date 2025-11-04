<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    @php
        $igreja = Auth::user()?->igreja; // relacionamento user->igreja
    @endphp
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo + Foto do Perfil/Igreja -->
                @php
                    $fotoIgreja = $igreja?->detalhesUsuario?->foto;
                    $fotoUsuario = Auth::user()?->detalhesUsuario?->foto;

                    // Define a foto que será exibida
                    if ($igreja && $fotoIgreja) {
                        $fotoParaMostrar = asset('storage/' . $fotoIgreja);
                        $alt = 'Foto da Igreja';
                    } elseif (!$igreja && $fotoUsuario) {
                        $fotoParaMostrar = asset('storage/' . $fotoUsuario);
                        $alt = 'Foto do Perfil';
                    } else {
                        $fotoParaMostrar = null; // sem foto, usar ícone padrão
                        $alt = 'Foto padrão';
                    }
                @endphp

                <div class="shrink-0 flex items-center gap-3">
                    <a href="{{ route('dashboard') }}" class="relative">
                        @if ($fotoParaMostrar)
                            <img
                                src="{{ $fotoParaMostrar }}"
                                alt="{{ $alt }}"
                                class="h-9 w-9 rounded-full object-cover border border-gray-300 hover:ring-2 hover:ring-indigo-400 transition duration-200"
                            >
                        @else
                            <!-- Ícone padrão -->
                            <div class="h-9 w-9 rounded-full bg-gray-200 flex items-center justify-center border border-gray-300 hover:ring-2 hover:ring-indigo-400 transition duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 7.5a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a8.25 8.25 0 0115 0" />
                                </svg>
                            </div>
                        @endif
                    </a>
                </div>



                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('protocolo.create')" :active="request()->routeIs('protocolo.create')">
                        Chamado
                    </x-nav-link>

                    <!-- Menu de Ministérios -->
                    <x-dropdown align="left" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                Ministérios
                                <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('ministerios.index')">Ministérios</x-dropdown-link>
                            <x-dropdown-link :href="route('ministerios.liderancas.index')">Lideranças</x-dropdown-link>
                            <x-dropdown-link :href="route('ministerios.comissoes.index')">Comissões</x-dropdown-link>
                            <x-dropdown-link :href="route('ministerios.integrantes.index')">Integrantes</x-dropdown-link>
                            <x-dropdown-link :href="route('ministerios.agendas.index')">Agenda</x-dropdown-link>
                        </x-slot>
                    </x-dropdown>

                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <!-- Notificações -->
                <div x-data="notificacoes()" x-init="carregar()" class="relative ms-4 z-50">
                    <button @click="abrir()" class="relative">
                        <!-- ícone do sininho -->
                        <svg class="h-6 w-6 text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" 
                                fill="none" viewBox="0 0 24 24" stroke-width="1.5" 
                                stroke="currentColor" class="h-6 w-6 text-gray-600">
                                <path stroke-linecap="round" stroke-linejoin="round" 
                                    d="M14.857 17.657A1.001 1.001 0 0016 17V9a4 4 0 10-8 0v8a1 1 0 001.143.657L12 18l2.857-.343z" />
                            </svg>
                        </svg>

                        <template x-if="count > 0">
                            <span class="absolute -top-1 -right-1 bg-red-600 text-white rounded-full text-xs px-1" x-text="count"></span>
                        </template>
                    </button>

                    <div x-show="open" @click.outside="fechar()" class="absolute right-0 mt-2 w-80 bg-white border rounded shadow z-50">
                        <div class="p-2 border-b flex justify-between items-center">
                            <div class="font-medium">Notificações</div>
                            <!--<button @click="marcarTodasLidas()" class="text-sm text-blue-600">Marcar todas como lidas</button>-->
                        </div>

                        <template x-for="n in notificacoes" :key="n.id">
                            <div class="p-3 border-b flex justify-between items-start">
                                <div>
                                    <div class="font-semibold" x-text="n.titulo"></div>
                                    <div class="text-sm text-gray-600" x-text="n.mensagem"></div>
                                    <div class="text-xs text-gray-400" x-text="n.created_at_formatted"></div>

                                </div>
                                <div class="ms-2">
                                    <button @click="ocultar(n.id)" class="text-xs text-gray-500">Ocultar</button>
                                </div>
                            </div>
                        </template>

                        <div x-show="notificacoes.length === 0" class="p-3 text-sm text-gray-500">Nenhuma notificação</div>
                    </div>
                </div>



                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()?->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        @if ($igreja)
                            <x-dropdown-link :href="route('profile.relacoes')">
                                {{ __('Minhas Relações') }}
                            </x-dropdown-link>
                        @endif

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Sair') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>

            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-nav-link :href="route('protocolo.create')" :active="request()->routeIs('protocolo.create')">
                Chamado
            </x-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()?->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()?->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
