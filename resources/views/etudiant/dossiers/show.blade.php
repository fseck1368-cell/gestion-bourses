<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dossier {{ $dossier->reference }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))<div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">{{ session('success') }}</div>@endif
            @if(session('error'))<div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">{{ session('error') }}</div>@endif

            <!-- Timeline visuelle -->
            <div class="bg-white shadow-sm sm:rounded-lg mb-6 p-6">
                <h3 class="text-lg font-medium mb-4">Suivi de votre dossier</h3>
                <div class="flex items-center justify-between mb-6">
                    @php
                        $etapes = [
                            ['label' => 'Soumis', 'active' => true],
                            ['label' => 'Assigné', 'active' => $dossier->instructeur_id !== null],
                            ['label' => 'En instruction', 'active' => in_array($dossier->statut, ['en_cours_instruction', 'accepte', 'rejete'])],
                            ['label' => 'Décision', 'active' => in_array($dossier->statut, ['accepte', 'rejete'])],
                        ];
                    @endphp
                    @foreach($etapes as $i => $etape)
                        <div class="flex-1 text-center">
                            <div class="w-10 h-10 mx-auto rounded-full flex items-center justify-center {{ $etape['active'] ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-500' }}">
                                {{ $i + 1 }}
                            </div>
                            <p class="text-xs mt-1 {{ $etape['active'] ? 'text-indigo-700 font-medium' : 'text-gray-400' }}">{{ $etape['label'] }}</p>
                        </div>
                        @if($i < count($etapes) - 1)
                            <div class="flex-1 h-0.5 {{ $etapes[$i+1]['active'] ? 'bg-indigo-600' : 'bg-gray-200' }}"></div>
                        @endif
                    @endforeach
                </div>

                @if($dossier->complement_requis && !$dossier->complement_date_reponse)
                    <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg flex items-start gap-2">
                        <span class="text-yellow-600 text-lg">!</span>
                        <div>
                            <p class="text-sm font-medium text-yellow-800">Complément demandé</p>
                            <p class="text-sm text-yellow-700">{{ $dossier->complement_description }}</p>
                            <p class="text-xs text-yellow-600 mt-1">Demandé le {{ $dossier->complement_date_demande->format('d/m/Y') }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Demande de complément : réponse étudiant -->
            @if($dossier->complement_requis && !$dossier->complement_date_reponse)
            <div class="bg-white shadow-sm sm:rounded-lg mb-6 p-6 border-l-4 border-yellow-500">
                <h3 class="text-lg font-medium mb-3">Répondre à la demande de complément</h3>
                <form method="POST" action="{{ route('etudiant.complement.repondre', $dossier) }}">
                    @csrf
                    <textarea name="reponse" rows="4" required class="w-full rounded-md border-gray-300 text-sm mb-3" placeholder="Fournissez les informations demandées..."></textarea>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Envoyer ma réponse</button>
                </form>
            </div>
            @endif

            <!-- Infos du dossier -->
            <div class="bg-white shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-lg font-medium">Informations du dossier</h3>
                        <span class="px-3 py-1 text-sm font-semibold rounded-full bg-{{ $dossier->statut_color }}-100 text-{{ $dossier->statut_color }}-800">
                            {{ $dossier->statut_label }}
                        </span>
                    </div>

                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div><dt class="text-sm text-gray-500">Année universitaire</dt><dd class="font-medium">{{ $dossier->annee_universitaire }}</dd></div>
                        <div><dt class="text-sm text-gray-500">Niveau</dt><dd class="font-medium">{{ $dossier->niveau_etude }}</dd></div>
                        <div><dt class="text-sm text-gray-500">Filière</dt><dd class="font-medium">{{ $dossier->filiere }}</dd></div>
                        <div><dt class="text-sm text-gray-500">Établissement</dt><dd class="font-medium">{{ $dossier->etablissement }}</dd></div>
                        <div><dt class="text-sm text-gray-500">Moyenne générale</dt><dd class="font-medium">{{ $dossier->moyenne_generale ?? '-' }}</dd></div>
                        <div><dt class="text-sm text-gray-500">Revenu familial</dt><dd class="font-medium">{{ $dossier->revenu_familial ? number_format($dossier->revenu_familial, 0, ',', ' ') . ' DH' : '-' }}</dd></div>
                    </dl>

                    @if($dossier->commentaire_instructeur)
                        <div class="mt-4 p-3 bg-blue-50 rounded">
                            <dt class="text-sm font-medium text-blue-800">Commentaire de l'instructeur</dt>
                            <dd class="mt-1 text-blue-700">{{ $dossier->commentaire_instructeur }}</dd>
                        </div>
                    @endif

                    @if($dossier->commentaire_admin)
                        <div class="mt-4 p-3 bg-purple-50 rounded">
                            <dt class="text-sm font-medium text-purple-800">Commentaire de l'administration</dt>
                            <dd class="mt-1 text-purple-700">{{ $dossier->commentaire_admin }}</dd>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Documents -->
            @if($dossier->documents->isNotEmpty())
                <div class="bg-white shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium mb-4">Documents joints</h3>
                        <ul class="divide-y divide-gray-200">
                            @foreach($dossier->documents as $doc)
                                <li class="py-3 flex justify-between items-center">
                                    <div>
                                        <span class="text-sm font-medium">{{ $doc->type_label }}</span>
                                        <span class="text-sm text-gray-500 ml-2">{{ $doc->nom_fichier }}</span>
                                    </div>
                                    <a href="{{ Storage::url($doc->chemin) }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 text-sm">Télécharger</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Demander un RDV -->
            @if($dossier->instructeur_id && $dossier->statut === 'en_cours_instruction')
            <div class="bg-white shadow-sm sm:rounded-lg mb-6 p-6">
                <h3 class="text-lg font-medium mb-3">Demander un rendez-vous</h3>
                <form method="POST" action="{{ route('etudiant.rendez-vous.store', $dossier) }}">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Date et heure souhaitées</label>
                            <input type="datetime-local" name="date_heure" required class="w-full rounded-md border-gray-300 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Motif</label>
                            <input type="text" name="motif" required class="w-full rounded-md border-gray-300 text-sm" placeholder="Ex: Clarifier ma situation">
                        </div>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm">Envoyer la demande</button>
                </form>
            </div>
            @endif

            <!-- Timeline historique -->
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium mb-4">Historique détaillé</h3>
                <div class="relative">
                    <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200"></div>
                    <div class="space-y-4">
                        @foreach($dossier->historiques as $h)
                        <div class="flex items-start gap-4 relative">
                            <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center z-10 flex-shrink-0">
                                <div class="w-3 h-3 rounded-full bg-indigo-500"></div>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-800">{{ $h->action }}</p>
                                <p class="text-xs text-gray-500">{{ $h->user->name }} — {{ $h->created_at->format('d/m/Y H:i') }}</p>
                                @if($h->commentaire)<p class="text-sm text-gray-600 mt-1">{{ $h->commentaire }}</p>@endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-6 flex gap-3">
                @if($dossier->statut === 'rejete')
                    <a href="{{ route('etudiant.recours.create', $dossier) }}" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm">Soumettre un recours</a>
                @endif
                <a href="{{ route('etudiant.dossiers.recepisse', $dossier) }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 text-sm">Télécharger récépissé</a>
            </div>
        </div>
    </div>
</x-app-layout>
