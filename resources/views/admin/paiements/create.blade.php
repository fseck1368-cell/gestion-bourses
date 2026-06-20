<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Nouveau Paiement</h2></x-slot>

    <div class="py-12"><div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm sm:rounded-lg p-6">
            <form method="POST" action="{{ route('admin.paiements.store') }}">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dossier (étudiant accepté)</label>
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
                        <label class="block text-sm font-medium text-gray-700 mb-1">Montant (DH)</label>
                        <input type="number" step="0.01" name="montant" value="{{ old('montant') }}" required class="w-full rounded-md border-gray-300">
                        @error('montant')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mode de paiement</label>
                        <select name="mode_paiement" required class="w-full rounded-md border-gray-300">
                            <option value="virement" {{ old('mode_paiement') == 'virement' ? 'selected' : '' }}>Virement</option>
                            <option value="cheque" {{ old('mode_paiement') == 'cheque' ? 'selected' : '' }}>Chèque</option>
                            <option value="especes" {{ old('mode_paiement') == 'especes' ? 'selected' : '' }}>Espèces</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Banque</label>
                        <input type="text" name="banque" value="{{ old('banque') }}" class="w-full rounded-md border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">N° Compte</label>
                        <input type="text" name="numero_compte" value="{{ old('numero_compte') }}" class="w-full rounded-md border-gray-300">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Référence bancaire</label>
                        <input type="text" name="reference_bancaire" value="{{ old('reference_bancaire') }}" class="w-full rounded-md border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date prévue</label>
                        <input type="date" name="date_prevue" value="{{ old('date_prevue') }}" class="w-full rounded-md border-gray-300">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Période</label>
                    <input type="text" name="periode" value="{{ old('periode') }}" placeholder="Ex: Septembre 2026" class="w-full rounded-md border-gray-300">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Commentaire</label>
                    <textarea name="commentaire" rows="3" class="w-full rounded-md border-gray-300">{{ old('commentaire') }}</textarea>
                </div>

                <div class="flex justify-end gap-2">
                    <a href="{{ route('admin.paiements.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md">Annuler</a>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Créer le paiement</button>
                </div>
            </form>
        </div>
    </div></div>
</x-app-layout>
