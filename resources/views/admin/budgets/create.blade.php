<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Nouveau Budget</h2></x-slot>

    <div class="py-12"><div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm sm:rounded-lg p-6">
            <form method="POST" action="{{ route('admin.budgets.store') }}">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Campagne</label>
                    <select name="campagne_id" required class="w-full rounded-md border-gray-300">
                        <option value="">-- Sélectionner --</option>
                        @foreach($campagnes as $c)
                            <option value="{{ $c->id }}" {{ old('campagne_id') == $c->id ? 'selected' : '' }}>{{ $c->nom }}</option>
                        @endforeach
                    </select>
                    @error('campagne_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Libellé</label>
                    <input type="text" name="libelle" value="{{ old('libelle') }}" required class="w-full rounded-md border-gray-300" placeholder="Ex: Budget bourses excellence 2026">
                    @error('libelle')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Montant alloué (DH)</label>
                        <input type="number" step="0.01" name="montant_alloue" value="{{ old('montant_alloue') }}" required class="w-full rounded-md border-gray-300">
                        @error('montant_alloue')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Année universitaire</label>
                        <input type="text" name="annee_universitaire" value="{{ old('annee_universitaire') }}" required class="w-full rounded-md border-gray-300" placeholder="2025-2026">
                        @error('annee_universitaire')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Source de financement</label>
                    <input type="text" name="source_financement" value="{{ old('source_financement') }}" class="w-full rounded-md border-gray-300" placeholder="Ex: Ministère, Fondation, etc.">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Observations</label>
                    <textarea name="observations" rows="3" class="w-full rounded-md border-gray-300">{{ old('observations') }}</textarea>
                </div>

                <div class="flex justify-end gap-2">
                    <a href="{{ route('admin.budgets.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md">Annuler</a>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Créer</button>
                </div>
            </form>
        </div>
    </div></div>
</x-app-layout>
