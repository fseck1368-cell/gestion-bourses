<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Modifier le critère : {{ $critere->nom }}</h2></x-slot>

    <div class="py-12"><div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm sm:rounded-lg p-6">
            <form method="POST" action="{{ route('admin.criteres.update', $critere) }}">
                @csrf @method('PUT')

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Campagne</label>
                    <select name="campagne_id" required class="w-full rounded-md border-gray-300">
                        @foreach($campagnes as $c)
                            <option value="{{ $c->id }}" {{ $critere->campagne_id == $c->id ? 'selected' : '' }}>{{ $c->nom }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom du critère</label>
                    <input type="text" name="nom" value="{{ old('nom', $critere->nom) }}" required class="w-full rounded-md border-gray-300">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="2" class="w-full rounded-md border-gray-300">{{ old('description', $critere->description) }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                        <select name="type" required class="w-full rounded-md border-gray-300">
                            <option value="numerique" {{ $critere->type == 'numerique' ? 'selected' : '' }}>Numérique</option>
                            <option value="booleen" {{ $critere->type == 'booleen' ? 'selected' : '' }}>Booléen</option>
                            <option value="selection" {{ $critere->type == 'selection' ? 'selected' : '' }}>Sélection</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Poids</label>
                        <input type="number" name="poids" value="{{ old('poids', $critere->poids) }}" min="1" max="10" required class="w-full rounded-md border-gray-300">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Valeur minimale</label>
                        <input type="number" step="0.01" name="valeur_min" value="{{ old('valeur_min', $critere->valeur_min) }}" class="w-full rounded-md border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Valeur maximale</label>
                        <input type="number" step="0.01" name="valeur_max" value="{{ old('valeur_max', $critere->valeur_max) }}" class="w-full rounded-md border-gray-300">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Valeurs acceptées</label>
                    <input type="text" name="valeurs_acceptees" value="{{ old('valeurs_acceptees', is_array($critere->valeurs_acceptees) ? implode(', ', $critere->valeurs_acceptees) : '') }}" class="w-full rounded-md border-gray-300">
                </div>

                <div class="mb-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="obligatoire" value="1" {{ $critere->obligatoire ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                        <span class="ml-2 text-sm text-gray-700">Critère obligatoire</span>
                    </label>
                </div>

                <div class="flex justify-end gap-2">
                    <a href="{{ route('admin.criteres.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md">Annuler</a>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Mettre à jour</button>
                </div>
            </form>
        </div>
    </div></div>
</x-app-layout>
