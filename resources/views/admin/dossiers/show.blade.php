<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dossier {{ $dossier->reference }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))<div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">{{ session('success') }}</div>@endif
            @if(session('error'))<div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">{{ session('error') }}</div>@endif

            <!-- Avis instructeur formel -->
            @if($dossier->avis_transmis_admin && $dossier->avis_instructeur)
            <div class="mb-6 p-4 rounded-lg border-l-4 border-{{ $dossier->avis_color }}-500 bg-{{ $dossier->avis_color }}-50">
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-{{ $dossier->avis_color }}-100 text-{{ $dossier->avis_color }}-800">Avis instructeur : {{ $dossier->avis_label }}</span>
                    <span class="text-xs text-gray-500">émis le {{ $dossier->date_avis_instructeur->format('d/m/Y') }} par {{ $dossier->instructeur?->name }}</span>
                </div>
                <p class="text-sm text-gray-700">{{ $dossier->commentaire_instructeur }}</p>
            </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-lg font-medium">Détails du dossier</h3>
                        <span class="px-3 py-1 text-sm font-semibold rounded-full bg-{{ $dossier->statut_color }}-100 text-{{ $dossier->statut_color }}-800">
                            {{ $dossier->statut_label }}
                        </span>
                    </div>

                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div><dt class="text-sm text-gray-500">Étudiant</dt><dd class="font-medium">{{ $dossier->etudiant->prenom }} {{ $dossier->etudiant->nom }}</dd></div>
                        <div><dt class="text-sm text-gray-500">Email</dt><dd class="font-medium">{{ $dossier->etudiant->email }}</dd></div>
                        <div><dt class="text-sm text-gray-500">Année</dt><dd class="font-medium">{{ $dossier->annee_universitaire }}</dd></div>
                        <div><dt class="text-sm text-gray-500">Niveau</dt><dd class="font-medium">{{ $dossier->niveau_etude }}</dd></div>
                        <div><dt class="text-sm text-gray-500">Filière</dt><dd class="font-medium">{{ $dossier->filiere }}</dd></div>
                        <div><dt class="text-sm text-gray-500">Établissement</dt><dd class="font-medium">{{ $dossier->etablissement }}</dd></div>
                        <div><dt class="text-sm text-gray-500">Moyenne</dt><dd class="font-medium">{{ $dossier->moyenne_generale ?? '-' }}/20</dd></div>
                        <div><dt class="text-sm text-gray-500">Revenu familial</dt><dd class="font-medium">{{ $dossier->revenu_familial ? number_format($dossier->revenu_familial, 0, ',', ' ') . ' DH' : '-' }}</dd></div>
                        <div><dt class="text-sm text-gray-500">Frères/Soeurs</dt><dd class="font-medium">{{ $dossier->nombre_freres_soeurs ?? '-' }}</dd></div>
                        <div><dt class="text-sm text-gray-500">Instructeur</dt><dd class="font-medium">{{ $dossier->instructeur ? $dossier->instructeur->name : 'Non assigné' }}</dd></div>
                    </dl>

                    @if($dossier->motif_demande)
                        <div class="mt-4"><dt class="text-sm text-gray-500">Motif</dt><dd class="mt-1">{{ $dossier->motif_demande }}</dd></div>
                    @endif
                    @if($dossier->situation_sociale)
                        <div class="mt-4"><dt class="text-sm text-gray-500">Situation sociale</dt><dd class="mt-1">{{ $dossier->situation_sociale }}</dd></div>
                    @endif
                </div>
            </div>

            @if($dossier->documents->isNotEmpty())
                <div class="bg-white shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium mb-3">Documents ({{ $dossier->documents->count() }})</h3>
                        <ul class="divide-y divide-gray-200">
                            @foreach($dossier->documents as $doc)
                                <li class="py-3 flex justify-between items-center">
                                    <span class="text-sm"><strong>{{ $doc->type_label }}</strong> - {{ $doc->nom_fichier }}</span>
                                    <a href="{{ Storage::url($doc->chemin) }}" target="_blank" class="text-indigo-600 text-sm">Voir</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Assignation -->
            @if($dossier->statut === 'soumis')
                <div class="bg-white shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium mb-4">Assigner à un instructeur</h3>
                        <form method="POST" action="{{ route('admin.dossiers.assigner', $dossier) }}" class="flex gap-4 items-end">
                            @csrf
                            <div class="flex-1">
                                <select name="instructeur_id" class="w-full rounded-md border-gray-300" required>
                                    <option value="">-- Choisir un instructeur --</option>
                                    @foreach($instructeurs as $inst)
                                        <option value="{{ $inst->id }}">{{ $inst->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Assigner</button>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Transfert de dossier -->
            @if($dossier->instructeur_id && $dossier->statut === 'en_cours_instruction')
                <div class="bg-white shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium mb-4">Transférer à un autre instructeur</h3>
                        <form method="POST" action="{{ route('admin.dossiers.transferer', $dossier) }}">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">Nouvel instructeur</label>
                                    <select name="instructeur_id" class="w-full rounded-md border-gray-300" required>
                                        <option value="">-- Sélectionner --</option>
                                        @foreach($instructeurs as $inst)
                                            @if($inst->id !== $dossier->instructeur_id)
                                                <option value="{{ $inst->id }}">{{ $inst->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">Motif du transfert</label>
                                    <input type="text" name="motif_transfert" required class="w-full rounded-md border-gray-300" placeholder="Ex: Absence, surcharge...">
                                </div>
                            </div>
                            <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 text-sm">Transférer</button>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Décision administrative -->
            @if(in_array($dossier->statut, ['soumis', 'en_cours_instruction']))
                <div class="bg-white shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium mb-4">Décision administrative</h3>
                        <form method="POST" action="{{ route('admin.dossiers.decision', $dossier) }}">
                            @csrf
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Décision</label>
                                    <select name="decision" class="mt-1 block w-full rounded-md border-gray-300" required>
                                        <option value="">-- Sélectionner --</option>
                                        <option value="accepte">Accepter la demande</option>
                                        <option value="rejete">Rejeter la demande</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Commentaire</label>
                                    <textarea name="commentaire" rows="3" class="mt-1 block w-full rounded-md border-gray-300"></textarea>
                                </div>
                                <div>
                                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700" onclick="return confirm('Confirmer cette décision ?')">
                                        Valider la décision
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Workflow approbation -->
            @if($dossier->statut === 'en_cours_instruction')
            <div class="bg-white shadow-sm sm:rounded-lg mb-6 p-6">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="text-lg font-medium">Workflow d'approbation</h3>
                    <a href="{{ route('admin.approbations.configurer', $dossier) }}" class="text-indigo-600 text-sm hover:underline">Configurer</a>
                </div>
                @if($dossier->approbations->count())
                    <div class="space-y-2">
                        @foreach($dossier->approbations as $app)
                        <div class="flex items-center gap-3 p-2 bg-gray-50 rounded">
                            <span class="w-6 h-6 flex items-center justify-center bg-indigo-100 text-indigo-800 rounded-full text-xs font-bold">{{ $app->ordre }}</span>
                            <span class="flex-1 text-sm">{{ $app->approbateur->name }}</span>
                            <span class="px-2 py-0.5 text-xs rounded-full bg-{{ $app->statut_color }}-100 text-{{ $app->statut_color }}-800">{{ $app->statut_label }}</span>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500">Aucun workflow configuré.</p>
                @endif
            </div>
            @endif

            <!-- Historique -->
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium mb-4">Historique</h3>
                    <div class="space-y-3">
                        @foreach($dossier->historiques as $h)
                            <div class="flex gap-3 text-sm border-l-2 border-indigo-200 pl-3">
                                <span class="text-gray-400 whitespace-nowrap">{{ $h->created_at->format('d/m/Y H:i') }}</span>
                                <div>
                                    <span class="font-medium">{{ $h->action }}</span>
                                    <span class="text-gray-500"> — {{ $h->user->name }}</span>
                                    @if($h->commentaire)<p class="text-gray-600 mt-1">{{ $h->commentaire }}</p>@endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Exports -->
            @if($dossier->statut === 'accepte')
            <div class="mt-6 flex gap-3">
                <a href="{{ route('admin.export.attestation', $dossier) }}" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm">Attestation PDF</a>
                @if($dossier->convention)
                    <a href="{{ route('admin.export.convention', $dossier->convention) }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">Convention PDF</a>
                @endif
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
