<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Nouvelle Convention</h2></x-slot>

    <div class="py-12"><div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm sm:rounded-lg p-6">
            <form method="POST" action="{{ route('admin.conventions.store') }}">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dossier accepté (sans convention)</label>
                    <select name="dossier_id" required class="w-full rounded-md border-gray-300">
                        <option value="">-- Sélectionner --</option>
                        @foreach($dossiers as $d)
                            <option value="{{ $d->id }}" {{ old('dossier_id') == $d->id ? 'selected' : '' }}>
                                {{ $d->reference }} - {{ $d->etudiant->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('dossier_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date début</label>
                        <input type="date" name="date_debut" value="{{ old('date_debut') }}" required class="w-full rounded-md border-gray-300">
                        @error('date_debut')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date fin</label>
                        <input type="date" name="date_fin" value="{{ old('date_fin') }}" required class="w-full rounded-md border-gray-300">
                        @error('date_fin')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Montant mensuel (DH)</label>
                        <input type="number" step="0.01" name="montant_mensuel" value="{{ old('montant_mensuel') }}" required class="w-full rounded-md border-gray-300">
                        @error('montant_mensuel')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Durée (mois)</label>
                        <input type="number" name="duree_mois" value="{{ old('duree_mois') }}" min="1" max="36" required class="w-full rounded-md border-gray-300">
                        @error('duree_mois')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Conditions</label>
                    <textarea name="conditions" rows="3" class="w-full rounded-md border-gray-300" placeholder="Conditions d'attribution de la bourse...">{{ old('conditions') }}</textarea>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Obligations de l'étudiant</label>
                    <textarea name="obligations_etudiant" rows="3" class="w-full rounded-md border-gray-300" placeholder="Obligations que l'étudiant doit respecter...">{{ old('obligations_etudiant') }}</textarea>
                </div>

                <div class="flex justify-end gap-2">
                    <a href="{{ route('admin.conventions.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md">Annuler</a>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Créer la convention</button>
                </div>
            </form>
        </div>
    </div></div>
</x-app-layout>
