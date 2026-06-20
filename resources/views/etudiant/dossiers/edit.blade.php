<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Modifier le dossier {{ $dossier->reference }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('etudiant.dossiers.update', $dossier) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Année universitaire *</label>
                                <input type="text" name="annee_universitaire" value="{{ old('annee_universitaire', $dossier->annee_universitaire) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Niveau d'étude *</label>
                                <select name="niveau_etude" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    @foreach(['Licence 1','Licence 2','Licence 3','Master 1','Master 2','Doctorat'] as $n)
                                        <option value="{{ $n }}" {{ old('niveau_etude', $dossier->niveau_etude) == $n ? 'selected' : '' }}>{{ $n }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Filière *</label>
                                <input type="text" name="filiere" value="{{ old('filiere', $dossier->filiere) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Établissement *</label>
                                <input type="text" name="etablissement" value="{{ old('etablissement', $dossier->etablissement) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Moyenne générale</label>
                                <input type="number" step="0.01" min="0" max="20" name="moyenne_generale" value="{{ old('moyenne_generale', $dossier->moyenne_generale) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Revenu familial (FCFA)</label>
                                <input type="number" name="revenu_familial" value="{{ old('revenu_familial', $dossier->revenu_familial) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nombre de frères/soeurs</label>
                                <input type="number" min="0" name="nombre_freres_soeurs" value="{{ old('nombre_freres_soeurs', $dossier->nombre_freres_soeurs) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700">Situation sociale</label>
                            <textarea name="situation_sociale" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('situation_sociale', $dossier->situation_sociale) }}</textarea>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700">Motif de la demande *</label>
                            <textarea name="motif_demande" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>{{ old('motif_demande', $dossier->motif_demande) }}</textarea>
                        </div>

                        @if($dossier->documents->isNotEmpty())
                            <div class="mb-6">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Documents existants</h4>
                                @foreach($dossier->documents as $doc)
                                    <div class="flex items-center justify-between py-2 border-b">
                                        <span class="text-sm">{{ $doc->type_label }} - {{ $doc->nom_fichier }}</span>
                                        <form method="POST" action="{{ route('etudiant.documents.destroy', $doc) }}" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 text-sm hover:text-red-800" onclick="return confirm('Supprimer ce document ?')">Supprimer</button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <div class="mb-6">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Ajouter des documents</h4>
                            <div class="flex gap-4">
                                <select name="types_documents[0]" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="releve_notes">Relevé de notes</option>
                                    <option value="certificat_scolarite">Certificat de scolarité</option>
                                    <option value="justificatif_revenu">Justificatif de revenu</option>
                                    <option value="piece_identite">Pièce d'identité</option>
                                    <option value="attestation_sociale">Attestation sociale</option>
                                    <option value="autre">Autre</option>
                                </select>
                                <input type="file" name="documents[0]" accept=".pdf,.jpg,.jpeg,.png" class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700">
                            </div>
                        </div>

                        <div class="flex justify-end gap-4 pt-4 border-t">
                            <a href="{{ route('etudiant.dossiers.show', $dossier) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">Annuler</a>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Mettre à jour</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
