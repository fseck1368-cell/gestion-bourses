<nav x-data="{ open: false }" class="bg-dark-950 border-b-2 border-primary-500 shadow-xl">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-primary-500 rounded-lg flex items-center justify-center">
                        <span class="text-dark-900 font-extrabold text-sm">GB</span>
                    </div>
                    <span class="text-white font-bold text-lg hidden sm:block">Gestion <span class="text-primary-400">Bourses</span></span>
                </a>

                <div class="hidden sm:flex sm:ms-10 sm:space-x-1">
                    <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-secondary-600 text-white' : 'text-dark-300 hover:bg-dark-800 hover:text-white' }} transition">
                        {{ __('Dashboard') }}
                    </a>

                    @if(auth()->user()->isEtudiant())
                        <a href="{{ route('etudiant.dossiers.create') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('etudiant.dossiers.create') ? 'bg-primary-500 text-dark-900' : 'text-dark-300 hover:bg-dark-800 hover:text-white' }} transition">
                            {{ __('New request') }}
                        </a>
                    @endif

                    @if(auth()->user()->isInstructeur())
                        <a href="{{ route('instructeur.dossiers.index') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('instructeur.dossiers.*') ? 'bg-secondary-600 text-white' : 'text-dark-300 hover:bg-dark-800 hover:text-white' }} transition">
                            {{ __('My files') }}
                        </a>
                        <a href="{{ route('instructeur.rendez-vous.index') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('instructeur.rendez-vous.*') ? 'bg-secondary-600 text-white' : 'text-dark-300 hover:bg-dark-800 hover:text-white' }} transition">
                            {{ __('Appointments') }}
                        </a>
                    @endif

                    @if(auth()->user()->isAdministrateur())
                        <a href="{{ route('admin.dossiers.index') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('admin.dossiers.*') ? 'bg-secondary-600 text-white' : 'text-dark-300 hover:bg-dark-800 hover:text-white' }} transition">
                            {{ __('Dossiers') }}
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('admin.users.*') ? 'bg-secondary-600 text-white' : 'text-dark-300 hover:bg-dark-800 hover:text-white' }} transition">
                            {{ __('Users') }}
                        </a>
                        <a href="{{ route('admin.campagnes.index') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('admin.campagnes.*') ? 'bg-secondary-600 text-white' : 'text-dark-300 hover:bg-dark-800 hover:text-white' }} transition">
                            {{ __('Campaigns') }}
                        </a>
                        <a href="{{ route('admin.commissions.index') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('admin.commissions.*') ? 'bg-secondary-600 text-white' : 'text-dark-300 hover:bg-dark-800 hover:text-white' }} transition">
                            {{ __('Commissions') }}
                        </a>

                        <div x-data="{ openMenu: false }" class="relative">
                            <button @click="openMenu = !openMenu" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('admin.paiements.*') || request()->routeIs('admin.criteres.*') || request()->routeIs('admin.evaluations.*') || request()->routeIs('admin.budgets.*') || request()->routeIs('admin.recours.*') || request()->routeIs('admin.conventions.*') || request()->routeIs('admin.rapports.*') ? 'bg-secondary-600 text-white' : 'text-dark-300 hover:bg-dark-800 hover:text-white' }} transition flex items-center gap-1">
                                {{ __('Management') }}
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                            </button>
                            <div x-show="openMenu" @click.away="openMenu = false" x-transition class="absolute left-0 mt-1 w-48 bg-dark-900 border border-dark-700 rounded-md shadow-lg z-50 max-h-80 overflow-y-auto">
                                <a href="{{ route('admin.paiements.index') }}" class="block px-4 py-2 text-sm text-dark-300 hover:bg-dark-800 hover:text-white">{{ __('Payments') }}</a>
                                <a href="{{ route('admin.budgets.index') }}" class="block px-4 py-2 text-sm text-dark-300 hover:bg-dark-800 hover:text-white">{{ __('Budgets') }}</a>
                                <a href="{{ route('admin.criteres.index') }}" class="block px-4 py-2 text-sm text-dark-300 hover:bg-dark-800 hover:text-white">{{ __('Criteria') }}</a>
                                <a href="{{ route('admin.evaluations.index') }}" class="block px-4 py-2 text-sm text-dark-300 hover:bg-dark-800 hover:text-white">{{ __('Evaluations') }}</a>
                                <a href="{{ route('admin.conventions.index') }}" class="block px-4 py-2 text-sm text-dark-300 hover:bg-dark-800 hover:text-white">{{ __('Conventions') }}</a>
                                <a href="{{ route('admin.recours.index') }}" class="block px-4 py-2 text-sm text-dark-300 hover:bg-dark-800 hover:text-white">{{ __('Appeals') }}</a>
                                <a href="{{ route('admin.rapports.index') }}" class="block px-4 py-2 text-sm text-dark-300 hover:bg-dark-800 hover:text-white">{{ __('Reports') }}</a>
                                <div class="border-t border-dark-700 my-1"></div>
                                <a href="{{ route('admin.approbations.index') }}" class="block px-4 py-2 text-sm text-dark-300 hover:bg-dark-800 hover:text-white">{{ __('Approvals') }}</a>
                                <a href="{{ route('admin.alertes.index') }}" class="block px-4 py-2 text-sm text-dark-300 hover:bg-dark-800 hover:text-white">{{ __('Alerts') }}</a>
                                <a href="{{ route('admin.audit.index') }}" class="block px-4 py-2 text-sm text-dark-300 hover:bg-dark-800 hover:text-white">{{ __('Audit') }}</a>
                                <a href="{{ route('admin.parametres.index') }}" class="block px-4 py-2 text-sm text-dark-300 hover:bg-dark-800 hover:text-white">{{ __('Settings') }}</a>
                                <a href="{{ route('admin.import.index') }}" class="block px-4 py-2 text-sm text-dark-300 hover:bg-dark-800 hover:text-white">{{ __('Import') }}</a>
                            </div>
                        </div>

                        <a href="{{ route('admin.statistiques') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('admin.statistiques') ? 'bg-primary-500 text-dark-900' : 'text-dark-300 hover:bg-dark-800 hover:text-white' }} transition">
                            {{ __('Statistics') }}
                        </a>
                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:gap-2">
                <x-language-switcher />

                <a href="{{ route('notifications.index') }}" class="relative p-2 text-dark-400 hover:text-primary-400 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    @if(auth()->user()->unreadNotifications->count() > 0)
                        <span class="absolute -top-1 -right-1 w-5 h-5 bg-primary-500 text-dark-900 text-xs font-bold rounded-full flex items-center justify-center">{{ auth()->user()->unreadNotifications->count() }}</span>
                    @endif
                </a>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium text-dark-300 hover:text-white hover:bg-dark-800 transition">
                            <div class="w-8 h-8 bg-secondary-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                {{ strtoupper(substr(auth()->user()->prenom, 0, 1)) }}{{ strtoupper(substr(auth()->user()->nom, 0, 1)) }}
                            </div>
                            <span>{{ Auth::user()->prenom }}</span>
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-2 border-b border-dark-700">
                            <p class="text-sm font-medium text-dark-200">{{ Auth::user()->prenom }} {{ Auth::user()->nom }}</p>
                            <p class="text-xs text-dark-400">{{ ucfirst(auth()->user()->role) }}</p>
                        </div>
                        <x-dropdown-link :href="route('profile.edit')">{{ __('Profile') }}</x-dropdown-link>
                        <x-dropdown-link :href="route('notifications.index')">{{ __('Notifications') }}</x-dropdown-link>
                        <x-dropdown-link :href="route('recherche')">{{ __('Search') }}</x-dropdown-link>
                        <div class="border-t border-dark-700 my-1"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-400 hover:bg-red-600 hover:text-white font-bold">
                                {{ __('Logout') }}
                            </button>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="p-2 rounded-md text-dark-400 hover:text-white hover:bg-dark-800 transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-dark-900">
        <div class="pt-2 pb-3 space-y-1 px-4">
            <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-md text-white hover:bg-dark-800">{{ __('Dashboard') }}</a>
        </div>
        <div class="pt-4 pb-3 border-t border-dark-700 px-4">
            <div class="text-white font-medium">{{ Auth::user()->prenom }} {{ Auth::user()->nom }}</div>
            <div class="text-dark-400 text-sm">{{ Auth::user()->email }}</div>
            <div class="mt-3 space-y-1">
                <a href="{{ route('profile.edit') }}" class="block px-3 py-2 rounded-md text-dark-300 hover:text-white hover:bg-dark-800">{{ __('Profile') }}</a>
                <form method="POST" action="{{ route('logout') }}">@csrf
                    <button type="submit" class="block w-full text-left px-3 py-2 rounded-md bg-red-600 text-white font-bold hover:bg-red-700">{{ __('Logout') }}</button>
                </form>
            </div>
        </div>
    </div>
</nav>
