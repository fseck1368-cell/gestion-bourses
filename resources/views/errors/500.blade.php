<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Erreur serveur - Gestion Bourses</title>
    @vite(['resources/css/app.css'])
</head>
<body class="font-sans antialiased bg-dark-50">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="text-center">
            <div class="w-24 h-24 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-12 h-12 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <h1 class="text-6xl font-extrabold text-dark-900 mb-4">500</h1>
            <h2 class="text-2xl font-bold text-dark-700 mb-2">Erreur serveur</h2>
            <p class="text-dark-500 mb-8 max-w-md mx-auto">Une erreur interne s'est produite. Veuillez réessayer plus tard.</p>
            <div class="flex justify-center gap-4">
                <a href="{{ url('/dashboard') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-primary-500 text-dark-900 font-bold rounded-lg hover:bg-primary-600 transition shadow-lg">
                    Retour au tableau de bord
                </a>
            </div>
        </div>
    </div>
</body>
</html>
