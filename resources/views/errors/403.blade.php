<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Accès refusé - Gestion Bourses</title>
    @vite(['resources/css/app.css'])
</head>
<body class="font-sans antialiased bg-dark-50">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="text-center">
            <div class="w-24 h-24 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-12 h-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>
            <h1 class="text-6xl font-extrabold text-dark-900 mb-4">403</h1>
            <h2 class="text-2xl font-bold text-dark-700 mb-2">Accès refusé</h2>
            <p class="text-dark-500 mb-8 max-w-md mx-auto">Vous n'avez pas les permissions nécessaires pour accéder à cette page.</p>
            <div class="flex justify-center gap-4">
                <a href="{{ url('/dashboard') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-primary-500 text-dark-900 font-bold rounded-lg hover:bg-primary-600 transition shadow-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    Retour au tableau de bord
                </a>
                <a href="javascript:history.back()" class="inline-flex items-center gap-2 px-6 py-3 bg-dark-200 text-dark-700 font-medium rounded-lg hover:bg-dark-300 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Page précédente
                </a>
            </div>
        </div>
    </div>
</body>
</html>
