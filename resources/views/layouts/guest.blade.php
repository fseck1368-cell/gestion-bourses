<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Gestion Bourses') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen flex">
            <!-- Left Panel - Branding -->
            <div class="hidden lg:flex lg:w-1/2 bg-dark-950 relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-secondary-900/50 to-dark-950"></div>
                <div class="absolute top-0 right-0 w-96 h-96 bg-primary-500/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="absolute bottom-0 left-0 w-72 h-72 bg-secondary-500/10 rounded-full translate-y-1/2 -translate-x-1/2"></div>

                <div class="relative z-10 flex flex-col justify-center px-12">
                    <div class="flex items-center gap-3 mb-8">
                        <div class="w-12 h-12 bg-primary-500 rounded-xl flex items-center justify-center">
                            <span class="text-dark-900 font-extrabold text-xl">GB</span>
                        </div>
                        <span class="text-white font-bold text-2xl">Gestion <span class="text-primary-400">Bourses</span></span>
                    </div>
                    <h1 class="text-4xl font-extrabold text-white mb-4 leading-tight">
                        Plateforme de gestion<br>des <span class="text-primary-400">bourses universitaires</span>
                    </h1>
                    <p class="text-dark-400 text-lg leading-relaxed">
                        Soumettez vos demandes de bourses, suivez leur avancement et recevez vos décisions en toute transparence.
                    </p>
                    <div class="mt-10 grid grid-cols-3 gap-4">
                        <div class="bg-dark-800/50 border border-dark-700 rounded-lg p-4 text-center">
                            <div class="text-primary-400 text-2xl font-bold">100%</div>
                            <div class="text-dark-400 text-xs mt-1">En ligne</div>
                        </div>
                        <div class="bg-dark-800/50 border border-dark-700 rounded-lg p-4 text-center">
                            <div class="text-secondary-400 text-2xl font-bold">24/7</div>
                            <div class="text-dark-400 text-xs mt-1">Accessible</div>
                        </div>
                        <div class="bg-dark-800/50 border border-dark-700 rounded-lg p-4 text-center">
                            <div class="text-primary-400 text-2xl font-bold">Sécurisé</div>
                            <div class="text-dark-400 text-xs mt-1">Données</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Panel - Form -->
            <div class="w-full lg:w-1/2 flex flex-col justify-center items-center px-6 py-12 bg-dark-50">
                <div class="lg:hidden flex items-center gap-2 mb-8">
                    <div class="w-10 h-10 bg-primary-500 rounded-lg flex items-center justify-center">
                        <span class="text-dark-900 font-extrabold">GB</span>
                    </div>
                    <span class="text-dark-900 font-bold text-xl">Gestion <span class="text-primary-600">Bourses</span></span>
                </div>

                <div class="w-full sm:max-w-md bg-white shadow-xl rounded-2xl border border-dark-200 p-8">
                    {{ $slot }}
                </div>

                <p class="mt-6 text-sm text-dark-400">&copy; {{ date('Y') }} Gestion Bourses. Tous droits réservés.</p>
            </div>
        </div>
    </body>
</html>
