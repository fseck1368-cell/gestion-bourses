<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Modifier le budget : {{ $budget->libelle }}</h2></x-slot>

    <div class="py-12"><div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm sm:rounded-lg p-6">
            <form method="POST" action="{{ route('admin.budgets.update', $budget) }}">
                @csrf @method('PUT')

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Campagne</label>
                    <select name="campagne_id" required class="w-full rounded-md border-gray-300">
                        @foreach($campagnes as $c)
                            <option value="{{ $c->id }}" {{ $budget->campagne_id == $c->id ? 'selected' : '' }}>{{ $c->nom }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Libellé</label>
                    <input type="text" name="libelle" value="{{ old('libelle', $budget->libelle) }}" required class="w-full rounded-md border-gray-300">
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Montant alloué (DH)</label>
                        <input type="number" step="0.01" name="montant_alloue" value="{{ old('montant_alloue', $budget->montant_alloue) }}" required class="w-full rounded-md border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Année universitaire</label>
                        <input type="text" name="annee_universitaire" value="{{ old('annee_universitaire', $budget->annee_universitaire) }}" required class="w-full rounded-md border-gray-300">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Source de financement</label>
                    <input type="text" name="source_financement" value="{{ old('source_financement', $budget->source_financement) }}" class="w-full rounded-md border-gray-300">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Observations</label>
                    <textarea name="observations" rows="3" class="w-full rounded-md border-gray-300">{{ old('observations', $budget->observations) }}</textarea>
                </div>

                <div class="flex justify-end gap-2">
                    <a href="{{ route('admin.budgets.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md">Annuler</a>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Mettre à jour</button>
                </div>
            </form>
        </div>
    </div></div>
</x-app-layout>
