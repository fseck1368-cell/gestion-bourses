<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Import d'étudiants</h2></x-slot>

    <div class="py-12"><div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        @if(session('success'))<div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">{{ session('success') }}</div>@endif
        @if(session('error'))<div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">{{ session('error') }}</div>@endif

        <div class="bg-white dark:bg-dark-800 shadow-sm sm:rounded-lg p-6 mb-6">
            <h3 class="font-semibold text-lg mb-4">Instructions</h3>
            <div class="text-sm text-gray-600 dark:text-gray-300 space-y-2">
                <p>Importez une liste d'étudiants à partir d'un fichier Excel (.xlsx, .xls) ou CSV.</p>
                <p><strong>Colonnes attendues :</strong></p>
                <ul class="list-disc list-inside ml-4">
                    <li><code>nom</code> — Nom de famille (obligatoire)</li>
                    <li><code>prenom</code> — Prénom (obligatoire)</li>
                    <li><code>email</code> — Adresse email (obligatoire, unique)</li>
                    <li><code>telephone</code> — Numéro de téléphone (optionnel)</li>
                    <li><code>numero_etudiant</code> — N° étudiant (optionnel)</li>
                </ul>
                <p class="mt-3">Les doublons (emails existants) seront ignorés. Un mot de passe aléatoire sera généré pour chaque étudiant.</p>
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.import.template') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm rounded-md hover:bg-green-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Télécharger le template
                </a>
            </div>
        </div>

        <div class="bg-white dark:bg-dark-800 shadow-sm sm:rounded-lg p-6">
            <h3 class="font-semibold text-lg mb-4">Importer un fichier</h3>
            <form method="POST" action="{{ route('admin.import.store') }}" enctype="multipart/form-data">
                @csrf
                <x-file-upload name="fichier" :multiple="false" accept=".xlsx,.xls,.csv" :maxSize="10" />
                @error('fichier')<p class="text-red-500 text-xs mt-2">{{ $message }}</p>@enderror

                <div class="mt-6 flex justify-end">
                    <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Lancer l'import</button>
                </div>
            </form>
        </div>
    </div></div>
</x-app-layout>
