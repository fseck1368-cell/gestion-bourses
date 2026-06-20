<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Nouveau Rapport Académique</h2></x-slot>

    <div class="py-12"><div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm sm:rounded-lg p-6">
            <form method="POST" action="{{ route('admin.rapports.store') }}">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Convention (active)</label>
                    <select name="convention_id" required class="w-full rounded-md border-gray-300">
                        <option value="">-- Sélectionner --</option>
                        @foreach($conventions as $c)
                            <option value="{{ $c->id }}" {{ old('convention_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->reference }} - {{ $c->etudiant->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('convention_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Semestre</label>
                        <select name="semestre" required class="w-full rounded-md border-gray-300">
                            <option value="S1" {{ old('semestre') == 'S1' ? 'selected' : '' }}>S1</option>
                            <option value="S2" {{ old('semestre') == 'S2' ? 'selected' : '' }}>S2</option>
                            <option value="S3" {{ old('semestre') == 'S3' ? 'selected' : '' }}>S3</option>
                            <option value="S4" {{ old('semestre') == 'S4' ? 'selected' : '' }}>S4</option>
                            <option value="S5" {{ old('semestre') == 'S5' ? 'selected' : '' }}>S5</option>
                            <option value="S6" {{ old('semestre') == 'S6' ? 'selected' : '' }}>S6</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Année universitaire</label>
                        <input type="text" name="annee_universitaire" value="{{ old('annee_universitaire') }}" required class="w-full rounded-md border-gray-300" placeholder="2025-2026">
                        @error('annee_universitaire')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Moyenne (/20)</label>
                        <input type="number" step="0.01" min="0" max="20" name="moyenne" value="{{ old('moyenne') }}" class="w-full rounded-md border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Crédits validés</label>
                        <input type="number" min="0" name="credits_valides" value="{{ old('credits_valides') }}" class="w-full rounded-md border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Crédits total</label>
                        <input type="number" min="0" name="credits_total" value="{{ old('credits_total') }}" class="w-full rounded-md border-gray-300">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Taux d'assiduité (%)</label>
                        <input type="number" step="0.01" min="0" max="100" name="taux_assiduite" value="{{ old('taux_assiduite') }}" class="w-full rounded-md border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Statut académique</label>
                        <select name="statut_academique" required class="w-full rounded-md border-gray-300">
                            <option value="bon" {{ old('statut_academique') == 'bon' ? 'selected' : '' }}>Bon</option>
                            <option value="acceptable" {{ old('statut_academique') == 'acceptable' ? 'selected' : '' }}>Acceptable</option>
                            <option value="insuffisant" {{ old('statut_academique') == 'insuffisant' ? 'selected' : '' }}>Insuffisant</option>
                            <option value="exclus" {{ old('statut_academique') == 'exclus' ? 'selected' : '' }}>Exclus</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="renouvellement_recommande" value="1" {{ old('renouvellement_recommande') ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                        <span class="ml-2 text-sm text-gray-700">Renouvellement recommandé</span>
                    </label>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Observations</label>
                    <textarea name="observations" rows="3" class="w-full rounded-md border-gray-300">{{ old('observations') }}</textarea>
                </div>

                <div class="flex justify-end gap-2">
                    <a href="{{ route('admin.rapports.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md">Annuler</a>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Enregistrer</button>
                </div>
            </form>
        </div>
    </div></div>
</x-app-layout>
