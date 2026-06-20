<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Recours {{ $recour->reference }}</h2></x-slot>

    <div class="py-12"><div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        @if(session('success'))<div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">{{ session('success') }}</div>@endif

        <div class="bg-white shadow-sm sm:rounded-lg p-6 mb-6">
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <h3 class="font-semibold text-lg mb-4">Informations du recours</h3>
                    <dl class="space-y-2">
                        <div><dt class="text-sm text-gray-500">Référence</dt><dd class="font-mono">{{ $recour->reference }}</dd></div>
                        <div><dt class="text-sm text-gray-500">Étudiant</dt><dd>{{ $recour->etudiant->name }}</dd></div>
                        <div><dt class="text-sm text-gray-500">Dossier concerné</dt><dd>{{ $recour->dossier->reference }}</dd></div>
                        <div><dt class="text-sm text-gray-500">Date de soumission</dt><dd>{{ $recour->date_soumission?->format('d/m/Y H:i') }}</dd></div>
                        <div><dt class="text-sm text-gray-500">Statut</dt><dd><span class="px-2 py-1 text-xs rounded-full bg-{{ $recour->statut_color }}-100 text-{{ $recour->statut_color }}-800">{{ $recour->statut_label }}</span></dd></div>
                    </dl>
                </div>
                <div>
                    <h3 class="font-semibold text-lg mb-4">Motif du recours</h3>
                    <p class="text-gray-700 whitespace-pre-line">{{ $recour->motif }}</p>
                    @if($recour->justification)
                        <h4 class="font-medium mt-4 mb-2">Justification</h4>
                        <p class="text-gray-700 whitespace-pre-line">{{ $recour->justification }}</p>
                    @endif
                </div>
            </div>
        </div>

        @if($recour->statut === 'soumis' || $recour->statut === 'en_examen')
        <div class="bg-white shadow-sm sm:rounded-lg p-6">
            <h3 class="font-semibold text-lg mb-4">Traiter le recours</h3>
            <form method="POST" action="{{ route('admin.recours.traiter', $recour) }}">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Décision</label>
                    <div class="flex gap-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="statut" value="accepte" class="text-green-600" required>
                            <span class="ml-2 text-sm">Accepter (réouvrir le dossier)</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="statut" value="rejete" class="text-red-600" required>
                            <span class="ml-2 text-sm">Rejeter</span>
                        </label>
                    </div>
                    @error('statut')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Motif de la décision</label>
                    <textarea name="decision_motif" rows="4" required class="w-full rounded-md border-gray-300" placeholder="Expliquez votre décision...">{{ old('decision_motif') }}</textarea>
                    @error('decision_motif')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Valider la décision</button>
            </form>
        </div>
        @endif

        @if($recour->decision_motif)
        <div class="bg-white shadow-sm sm:rounded-lg p-6 mt-6">
            <h3 class="font-semibold text-lg mb-2">Décision rendue</h3>
            <p class="text-sm text-gray-500 mb-2">Par {{ $recour->traitePar?->name }} le {{ $recour->date_traitement?->format('d/m/Y') }}</p>
            <p class="text-gray-700 whitespace-pre-line">{{ $recour->decision_motif }}</p>
        </div>
        @endif
    </div></div>
</x-app-layout>
