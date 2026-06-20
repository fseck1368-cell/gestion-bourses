<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Soumettre un recours</h2></x-slot>

    <div class="py-12"><div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <!-- Info dossier rejeté -->
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg mb-6">
            <p class="text-sm text-red-700">
                <strong>Dossier {{ $dossier->reference }}</strong> — rejeté le {{ $dossier->date_decision?->format('d/m/Y') }}
            </p>
            @if($dossier->commentaire_admin)
                <p class="text-sm text-red-600 mt-1">Motif : {{ $dossier->commentaire_admin }}</p>
            @endif
        </div>

        <div class="bg-white shadow-sm sm:rounded-lg p-6">
            <form method="POST" action="{{ route('etudiant.recours.store', $dossier) }}">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Motif du recours *</label>
                    <textarea name="motif" rows="5" required class="w-full rounded-md border-gray-300" placeholder="Expliquez pourquoi vous contestez cette décision...">{{ old('motif') }}</textarea>
                    @error('motif')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Justification complémentaire</label>
                    <textarea name="justification" rows="4" class="w-full rounded-md border-gray-300" placeholder="Informations ou éléments supplémentaires pour appuyer votre recours...">{{ old('justification') }}</textarea>
                    @error('justification')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="flex justify-end gap-2">
                    <a href="{{ route('etudiant.dossiers.show', $dossier) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md">Annuler</a>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Soumettre le recours</button>
                </div>
            </form>
        </div>
    </div></div>
</x-app-layout>
