<nav x-data="{ open: false }" class="bg-gray-800 border-b border-gray-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        {{-- Tu logo desde public/images/logo.png --}}
                        <img src="{{ asset('images/logo.png') }}" alt="Mi Logo" class="block h-9 w-auto fill-current text-white" />
                    </a>
                </div>
            </div>

            {{-- ZONA DE NAVEGACIÓN CENTRADA --}}
            <div class="hidden sm:flex sm:items-center">
                <div class="flex space-x-8">
                    {{-- Botón de Dashboard --}}
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-gray-300 hover:text-white" :active-classes="'text-white border-indigo-400'">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    {{-- Botón de Clientes --}}
                    <x-nav-link :href="route('clientes.index')" :active="request()->routeIs('clientes.index')" class="text-gray-300 hover:text-white" :active-classes="'text-white border-indigo-400'">
                        {{ __('Clientes') }}
                    </x-nav-link>
                    
                    {{-- Botón de Productos --}}
                    <x-nav-link :href="route('productos.index')" :active="request()->routeIs('productos.index')" class="text-gray-300 hover:text-white" :active-classes="'text-white border-indigo-400'">
                        {{ __('Productos') }}
                    </x-nav-link>
                    
                    {{-- Botón de Stock --}}
                    <x-nav-link :href="route('stock.index')" :active="request()->routeIs('stock.index')" class="text-gray-300 hover:text-white" :active-classes="'text-white border-indigo-400'">
                        {{ __('Stock') }}
                    </x-nav-link>
                    
                    {{-- Botón de Calendario --}}
                    <x-nav-link :href="route('calendario.index')" :active="request()->routeIs('calendario.index')" class="text-gray-300 hover:text-white" :active-classes="'text-white border-indigo-400'">
                        {{ __('Calendario') }}
                    </x-nav-link>
                    
                    {{-- Nuevo Botón de Ventas --}}
                    <x-nav-link :href="route('ventas.index')" :active="request()->routeIs('ventas.index')" class="text-gray-300 hover:text-white" :active-classes="'text-white border-indigo-400'">
                        {{ __('Ventas') }}
                    </x-nav-link>
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-300 bg-gray-800 hover:text-white hover:bg-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ml-1">
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
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-white hover:bg-gray-700 focus:outline-none focus:bg-gray-700 focus:text-white transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-gray-700">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-gray-300 hover:text-white">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('clientes.index')" :active="request()->routeIs('clientes.index')" class="text-gray-300 hover:text-white">
                {{ __('Clientes') }}
            </x-responsive-nav-link>
            
            <x-responsive-nav-link :href="route('productos.index')" :active="request()->routeIs('productos.index')" class="text-gray-300 hover:text-white">
                {{ __('Productos') }}
            </x-responsive-nav-link>
            
            <x-responsive-nav-link :href="route('stock.index')" :active="request()->routeIs('stock.index')" class="text-gray-300 hover:text-white">
                {{ __('Stock') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('calendario.index')" :active="request()->routeIs('calendario.index')" class="text-gray-300 hover:text-white">
                {{ __('Calendario') }}
            </x-responsive-nav-link>
            
            {{-- Nuevo Botón de Ventas Responsive --}}
            <x-responsive-nav-link :href="route('ventas.index')" :active="request()->routeIs('ventas.index')" class="text-gray-300 hover:text-white">
                {{ __('Ventas') }}
            </x-responsive-nav-link>
        </div>

        <div class="pt-4 pb-1 border-t border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-400">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" class="text-gray-300 hover:text-white">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();" class="text-gray-300 hover:text-white">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>