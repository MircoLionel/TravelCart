<nav x-data="{ open: false, dark: document.documentElement.classList.contains('dark') }"
     @theme-changed.window="dark = $event.detail === 'dark'"
     class="relative z-30 bg-white/95 border-b border-gray-200 shadow-sm transition dark:bg-gray-950/95 dark:border-gray-800">
    <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-indigo-500/40 via-sky-400/40 to-emerald-400/40"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center gap-8">
                <div class="shrink-0 flex items-center gap-3">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <x-application-logo class="block h-9 w-auto" />
                        <span class="text-lg font-semibold tracking-tight text-gray-900 dark:text-gray-100 hidden sm:inline">Welcome Group TravelCart</span>
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        Dashboard
                    </x-nav-link>

                    <x-nav-link :href="route('tours.index')" :active="request()->routeIs('tours.*')">
                        Catálogo
                    </x-nav-link>

                    @auth
                        @php($isVendor = Auth::user()?->isVendor() || Auth::user()?->isAdmin())
                        @if($isVendor)
                            @php($isVendorActive = request()->routeIs('vendor.*'))
                            <x-dropdown align="left" width="56">
                                <x-slot name="trigger">
                                    <button
                                        class="@class([
                                            'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 focus-visible:ring-offset-white dark:focus-visible:ring-offset-gray-900',
                                            'border-indigo-400 text-gray-900 dark:text-indigo-100' => $isVendorActive,
                                            'border-transparent text-gray-600 hover:text-gray-800 hover:border-gray-300 dark:text-gray-300 dark:hover:text-white dark:hover:border-gray-600' => ! $isVendorActive,
                                        ])"
                                    >
                                        Proveedor
                                        @if($isVendorActive)
                                            <span class="ml-2 inline-flex h-2 w-2 rounded-full bg-indigo-500"></span>
                                        @endif
                                        <svg class="ms-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <x-dropdown-link :href="route('vendor.tours.index')" :active="request()->routeIs('vendor.tours.*')">
                                        Mis viajes
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('vendor.buyers.index')" :active="request()->routeIs('vendor.buyers.*')">
                                        Compradores
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('vendor.reservations.index')" :active="request()->routeIs('vendor.reservations.*')">
                                        Reservas
                                    </x-dropdown-link>
                                </x-slot>
                            </x-dropdown>
                        @endif

                        <x-nav-link :href="route('orders.index')" :active="request()->routeIs('orders.*')">
                            Mis órdenes
                        </x-nav-link>

                        <x-nav-link :href="route('cart.show')" :active="request()->routeIs('cart.*')">
                            Carrito
                        </x-nav-link>
                    @endauth

                    @can('admin')
                        @php($isAdminActive = request()->routeIs('admin.*'))
                        @php($pendingUsersCount = \App\Models\User::query()
                            ->where(function ($q) {
                                $q->whereNull('role')->orWhere('is_approved', false);
                            })
                            ->count())
                        <x-dropdown align="left" width="56">
                            <x-slot name="trigger">
                                <button
                                    class="@class([
                                        'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 focus-visible:ring-offset-white dark:focus-visible:ring-offset-gray-900',
                                        'border-indigo-400 text-gray-900 dark:text-indigo-100' => $isAdminActive,
                                        'border-transparent text-gray-600 hover:text-gray-800 hover:border-gray-300 dark:text-gray-300 dark:hover:text-white dark:hover:border-gray-600' => ! $isAdminActive,
                                    ])"
                                >
                                    Admin
                                    @if($pendingUsersCount > 0)
                                        <span class="ml-2 inline-flex items-center gap-1 rounded-full bg-amber-100 px-2 py-0.5 text-[11px] font-semibold text-amber-800 dark:bg-amber-900/60 dark:text-amber-100">
                                            {{ $pendingUsersCount }}
                                        </span>
                                    @elseif($isAdminActive)
                                        <span class="ml-2 inline-flex h-2 w-2 rounded-full bg-indigo-500"></span>
                                    @endif
                                    <svg class="ms-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                                    Usuarios
                                    @if($pendingUsersCount > 0)
                                        <span class="ml-2 rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-semibold text-amber-800 dark:bg-amber-900/60 dark:text-amber-100">{{ $pendingUsersCount }}</span>
                                    @endif
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('admin.vendors.index')" :active="request()->routeIs('admin.vendors.*')">
                                    Proveedores
                                </x-dropdown-link>
                            </x-slot>
                        </x-dropdown>
                    @endcan
                </div>
            </div>

            @auth
                <div class="hidden sm:flex sm:items-center sm:ms-6 gap-3">
                    <button
                        @click="window.toggleTheme(); dark = !dark"
                        type="button"
                        class="inline-flex items-center justify-center rounded-full bg-gray-100 px-3 py-2 text-sm font-medium text-gray-700 shadow-sm transition hover:-translate-y-[1px] hover:text-indigo-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 focus-visible:ring-offset-white dark:bg-gray-800 dark:text-gray-200 dark:hover:text-amber-300 dark:focus-visible:ring-offset-gray-900"
                        :aria-label="dark ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro'"
                        title="Alternar modo"
                    >
                        <span class="hidden sm:inline" x-text="dark ? 'Modo noche' : 'Modo día'"></span>
                        <svg x-show="!dark" xmlns="http://www.w3.org/2000/svg" class="ml-2 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10 3a1 1 0 011 1v1a1 1 0 11-2 0V4a1 1 0 011-1zM10 14a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM4.222 5.636a1 1 0 011.414 0l.707.707a1 1 0 01-1.414 1.414l-.707-.707a1 1 0 010-1.414zM13.657 15.071a1 1 0 011.414 0l.707.707a1 1 0 01-1.414 1.414l-.707-.707a1 1 0 010-1.414zM3 10a1 1 0 011-1h1a1 1 0 110 2H4a1 1 0 01-1-1zm11-1a1 1 0 100 2h1a1 1 0 100-2h-1zM6.343 15.071a1 1 0 010 1.414l-.707.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM15.778 5.636a1 1 0 010 1.414l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 0z" />
                            <path d="M10 6a4 4 0 100 8 4 4 0 000-8z" />
                        </svg>
                        <svg x-show="dark" xmlns="http://www.w3.org/2000/svg" class="ml-2 h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M21 12.79A9 9 0 1111.21 3a7 7 0 109.79 9.79z" />
                        </svg>
                    </button>

                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-gray-200 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white shadow-sm transition hover:-translate-y-[1px] hover:text-indigo-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 focus-visible:ring-offset-white dark:border-gray-800 dark:bg-gray-900 dark:text-gray-200 dark:hover:text-white dark:focus-visible:ring-offset-gray-900">
                                <div>{{ Auth::user()->name }}</div>
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                Perfil
                            </x-dropdown-link>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                    Cerrar sesión
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            @else
                <div class="hidden sm:flex sm:items-center sm:ms-6 gap-3">
                    <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">Iniciar sesión</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">Registrarse</a>
                    @endif
                </div>
            @endauth

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

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-gray-200 dark:border-gray-800 bg-white/95 dark:bg-gray-950/95">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">Dashboard</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('tours.index')" :active="request()->routeIs('tours.*')">Catálogo</x-responsive-nav-link>

            @auth
                <x-responsive-nav-link :href="route('orders.index')" :active="request()->routeIs('orders.*')">Mis órdenes</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('cart.show')" :active="request()->routeIs('cart.*')">Carrito</x-responsive-nav-link>

                @php($isVendor = Auth::user()?->isVendor() || Auth::user()?->isAdmin())
                @if($isVendor)
                    <div class="px-4 pt-2 text-xs text-gray-400">Proveedor</div>
                    <x-responsive-nav-link :href="route('vendor.tours.index')" :active="request()->routeIs('vendor.tours.*')">Mis viajes</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('vendor.buyers.index')" :active="request()->routeIs('vendor.buyers.*')">Compradores</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('vendor.reservations.index')" :active="request()->routeIs('vendor.reservations.*')">Reservas</x-responsive-nav-link>
                @endif
            @endauth

            @can('admin')
                <div class="px-4 pt-2 text-xs text-gray-400">Admin</div>
                <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">Usuarios</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.vendors.index')" :active="request()->routeIs('admin.vendors.*')">Proveedores</x-responsive-nav-link>
            @endcan

            <div class="px-4 flex items-center gap-3">
                <span class="text-sm text-gray-500 dark:text-gray-300">Modo <span x-text="dark ? 'oscuro' : 'claro'"></span></span>
                <button @click="window.toggleTheme(); dark = !dark" type="button" class="inline-flex items-center rounded-full bg-gray-100 px-3 py-2 text-xs font-semibold text-gray-700 shadow-sm transition hover:scale-105 dark:bg-gray-800 dark:text-gray-100">
                    <span x-show="!dark">Activar</span>
                    <span x-show="dark">Desactivar</span>
                </button>
            </div>
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-800">
            @auth
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800 dark:text-gray-100">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">Perfil</x-responsive-nav-link>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">Cerrar sesión</x-responsive-nav-link>
                    </form>
                </div>
            @else
                <div class="px-4">
                    <x-responsive-nav-link :href="route('login')">Iniciar sesión</x-responsive-nav-link>
                    @if (Route::has('register'))
                        <x-responsive-nav-link :href="route('register')">Registrarse</x-responsive-nav-link>
                    @endif
                </div>
            @endauth
        </div>
    </div>
</nav>
