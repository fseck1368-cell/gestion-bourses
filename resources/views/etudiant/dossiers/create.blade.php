<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nouvelle demande de bourse</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Vérification d'éligibilité -->
            @if(isset($eligibilite))
                @if(!$eligibilite['eligible'])
                    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg">
                        <h4 class="font-bold text-red-800 mb-2">Vous ne pouvez pas soumettre de dossier</h4>
                        <ul class="text-sm text-red-700 list-disc list-inside">
                            @foreach($eligibilite['erreurs'] as $erreur)
                                <li>{{ $erreur }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if(!empty($eligibilite['avertissements']))
                    <div class="mb-6 p-4 bg-yellow-50 border-l-4 border-yellow-500 rounded-r-lg">
                        <h4 class="font-bold text-yellow-800 mb-2">Attention</h4>
                        <ul class="text-sm text-yellow-700 list-disc list-inside">
                            @foreach($eligibilite['avertissements'] as $avert)
                                <li>{{ $avert }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('etudiant.dossiers.store') }}" enctype="multipart/form-data">
                        @csrf

                        <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Informations académiques</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Année universitaire *</label>
                                <input type="text" name="annee_universitaire" value="{{ old('annee_universitaire', date('Y').'-'.(date('Y')+1)) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                @error('annee_universitaire') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Niveau d'étude *</label>
                                <select name="niveau_etude" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="">-- Sélectionner --</option>
                                    <option value="Licence 1" {{ old('niveau_etude') == 'Licence 1' ? 'selected' : '' }}>Licence 1</option>
                                    <option value="Licence 2" {{ old('niveau_etude') == 'Licence 2' ? 'selected' : '' }}>Licence 2</option>
                                    <option value="Licence 3" {{ old('niveau_etude') == 'Licence 3' ? 'selected' : '' }}>Licence 3</option>
                                    <option value="Master 1" {{ old('niveau_etude') == 'Master 1' ? 'selected' : '' }}>Master 1</option>
                                    <option value="Master 2" {{ old('niveau_etude') == 'Master 2' ? 'selected' : '' }}>Master 2</option>
                                    <option value="Doctorat" {{ old('niveau_etude') == 'Doctorat' ? 'selected' : '' }}>Doctorat</option>
                                </select>
                                @error('niveau_etude') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Filière *</label>
                                <input type="text" name="filiere" value="{{ old('filiere') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                @error('filiere') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Établissement *</label>
                                <input type="text" name="etablissement" value="{{ old('etablissement') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                @error('etablissement') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Moyenne générale</label>
                                <input type="number" step="0.01" min="0" max="20" name="moyenne_generale" value="{{ old('moyenne_generale') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('moyenne_generale') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Situation sociale</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Revenu familial mensuel (FCFA)</label>
                                <input type="number" name="revenu_familial" value="{{ old('revenu_familial') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('revenu_familial') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nombre de frères et soeurs</label>
                                <input type="number" min="0" name="nombre_freres_soeurs" value="{{ old('nombre_freres_soeurs') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('nombre_freres_soeurs') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Description de la situation sociale</label>
                                <textarea name="situation_sociale" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('situation_sociale') }}</textarea>
                                @error('situation_sociale') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Motivation</h3>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700">Motif de la demande *</label>
                            <textarea name="motif_demande" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>{{ old('motif_demande') }}</textarea>
                            @error('motif_demande') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Documents justificatifs</h3>

                        <div class="mb-6" x-data="{ files: [{ id: 1 }] }">
                            <template x-for="(file, index) in files" :key="file.id">
                                <div class="flex gap-4 mb-3 items-end">
                                    <div class="flex-1">
                                        <label class="block text-sm font-medium text-gray-700">Type de document</label>
                                        <select :name="'types_documents[' + index + ']'" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="releve_notes">Relevé de notes</option>
                                            <option value="certificat_scolarite">Certificat de scolarité</option>
                                            <option value="justificatif_revenu">Justificatif de revenu</option>
                                            <option value="piece_identite">Pièce d'identité</option>
                                            <option value="attestation_sociale">Attestation sociale</option>
                                            <option value="autre">Autre</option>
                                        </select>
                                    </div>
                                    <div class="flex-1">
                                        <label class="block text-sm font-medium text-gray-700">Fichier (PDF, JPG, PNG - max 5Mo)</label>
                                        <input type="file" :name="'documents[' + index + ']'" accept=".pdf,.jpg,.jpeg,.png" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                    </div>
                                    <button type="button" @click="files.splice(index, 1)" x-show="files.length > 1" class="px-3 py-2 text-red-600 hover:text-red-800">
                                        &times;
                                    </button>
                                </div>
                            </template>
                            <button type="button" @click="files.push({ id: Date.now() })" class="mt-2 text-sm text-indigo-600 hover:text-indigo-800">
                                + Ajouter un document
                            </button>
                        </div>

                        <div class="flex justify-end gap-4 pt-4 border-t">
                            <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">Annuler</a>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Soumettre la demande</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
