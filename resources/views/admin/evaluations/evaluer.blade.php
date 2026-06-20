<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Évaluer le dossier {{ $dossier->reference }}</h2></x-slot>

    <div class="py-12"><div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Info dossier -->
        <div class="bg-white shadow-sm sm:rounded-lg p-6 mb-6">
            <div class="grid grid-cols-3 gap-4">
                <div><p class="text-sm text-gray-500">Étudiant</p><p class="font-medium">{{ $dossier->etudiant->name }}</p></div>
                <div><p class="text-sm text-gray-500">Filière</p><p class="font-medium">{{ $dossier->filiere }}</p></div>
                <div><p class="text-sm text-gray-500">Moyenne</p><p class="font-medium">{{ $dossier->moyenne_generale ?? '-' }}</p></div>
            </div>
        </div>

        <!-- Formulaire évaluation -->
        <div class="bg-white shadow-sm sm:rounded-lg p-6">
            @if($criteres->isEmpty())
                <p class="text-gray-500 text-center">Aucun critère défini pour cette campagne. <a href="{{ route('admin.criteres.create') }}" class="text-indigo-600">Créer des critères</a></p>
            @else
                <form method="POST" action="{{ route('admin.evaluations.store', $dossier) }}">
                    @csrf

                    <div class="space-y-6">
                        @foreach($criteres as $critere)
                        <div class="border rounded-lg p-4">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h4 class="font-medium">{{ $critere->nom }}</h4>
                                    @if($critere->description)<p class="text-sm text-gray-500">{{ $critere->description }}</p>@endif
                                </div>
                                <span class="text-xs bg-gray-100 px-2 py-1 rounded">Poids: {{ $critere->poids }}</span>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">Note (0-20)</label>
                                    <input type="number" step="0.5" min="0" max="20" name="notes[{{ $critere->id }}]" value="{{ $evaluations[$critere->id] ?? '' }}" class="w-full rounded-md border-gray-300">
                                    @if($critere->valeur_min)<p class="text-xs text-gray-400 mt-1">Min requis: {{ $critere->valeur_min }}</p>@endif
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">Commentaire</label>
                                    <input type="text" name="commentaires[{{ $critere->id }}]" class="w-full rounded-md border-gray-300" placeholder="Optionnel">
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="mt-6 flex justify-end gap-2">
                        <a href="{{ route('admin.evaluations.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md">Retour</a>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Enregistrer l'évaluation</button>
                    </div>
                </form>
            @endif
        </div>
    </div></div>
</x-app-layout>
