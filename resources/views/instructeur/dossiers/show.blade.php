<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Instruction - {{ $dossier->reference }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))<div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">{{ session('success') }}</div>@endif
            @if(session('error'))<div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">{{ session('error') }}</div>@endif

            <!-- Score d'éligibilité automatique -->
            @if(isset($scoring))
            <div class="bg-white shadow-sm sm:rounded-lg mb-6 p-6 border-l-4 {{ $scoring['recommandation'] === 'favorable' ? 'border-green-500' : 'border-red-500' }}">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium">Score d'éligibilité</h3>
                    <div class="flex items-center gap-3">
                        <span class="text-2xl font-extrabold {{ $scoring['score_global'] >= $scoring['seuil'] ? 'text-green-600' : 'text-red-600' }}">{{ $scoring['score_global'] }}/20</span>
                        <span class="px-3 py-1 text-sm font-bold rounded-full {{ $scoring['recommandation'] === 'favorable' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            Recommandation : {{ ucfirst($scoring['recommandation']) }}
                        </span>
                    </div>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3 mb-4">
                    <div class="h-3 rounded-full transition-all {{ $scoring['score_global'] >= $scoring['seuil'] ? 'bg-green-500' : 'bg-red-500' }}" style="width: {{ min(($scoring['score_global'] / 20) * 100, 100) }}%"></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                    @foreach($scoring['details'] as $detail)
                    <div class="flex justify-between items-center py-1 px-2 bg-gray-50 rounded text-sm">
                        <span class="text-gray-700">{{ $detail['critere'] }}: <span class="text-gray-500">{{ $detail['valeur'] }}</span></span>
                        <span class="font-semibold {{ $detail['points'] >= $detail['max'] * 0.6 ? 'text-green-600' : 'text-orange-600' }}">{{ $detail['points'] }}/{{ $detail['max'] }}</span>
                    </div>
                    @endforeach
                </div>
                <p class="text-xs text-gray-500 mt-3">Seuil d'acceptation : {{ $scoring['seuil'] }}/20. Ce score est indicatif et aide à la prise de décision.</p>
            </div>
            @endif

            <!-- Timeline du dossier -->
            <div class="bg-white shadow-sm sm:rounded-lg mb-6 p-6">
                <h3 class="text-lg font-medium mb-4">Timeline du dossier</h3>
                <div class="relative">
                    <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200"></div>
                    <div class="space-y-4">
                        @foreach($dossier->historiques as $h)
                        <div class="flex items-start gap-4 relative">
                            <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center z-10 flex-shrink-0">
                                <div class="w-3 h-3 rounded-full bg-indigo-500"></div>
                            </div>
                            <div class="flex-1 pb-2">
                                <p class="text-sm font-medium text-gray-800">{{ $h->action }}</p>
                                <p class="text-xs text-gray-500">{{ $h->user?->name ?? 'Système' }} — {{ $h->created_at->format('d/m/Y H:i') }}</p>
                                @if($h->commentaire)<p class="text-sm text-gray-600 mt-1">{{ $h->commentaire }}</p>@endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Info étudiant -->
            <div class="bg-white shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-lg font-medium">Informations de l'étudiant</h3>
                        <span class="px-3 py-1 text-sm font-semibold rounded-full bg-{{ $dossier->statut_color }}-100 text-{{ $dossier->statut_color }}-800">
                            {{ $dossier->statut_label }}
                        </span>
                    </div>

                    @if($dossier->complement_requis && !$dossier->complement_date_reponse)
                        <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <p class="text-sm font-medium text-yellow-800">Complément demandé le {{ $dossier->complement_date_demande->format('d/m/Y') }}</p>
                            <p class="text-sm text-yellow-700">{{ $dossier->complement_description }}</p>
                            <p class="text-xs text-yellow-600 mt-1">En attente de réponse de l'étudiant...</p>
                        </div>
                    @elseif($dossier->complement_requis && $dossier->complement_date_reponse)
                        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                            <p class="text-sm font-medium text-green-800">Complément reçu le {{ $dossier->complement_date_reponse->format('d/m/Y') }}</p>
                        </div>
                    @endif

                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div><dt class="text-sm text-gray-500">Étudiant</dt><dd class="font-medium">{{ $dossier->etudiant->prenom }} {{ $dossier->etudiant->nom }}</dd></div>
                        <div><dt class="text-sm text-gray-500">Email</dt><dd class="font-medium">{{ $dossier->etudiant->email }}</dd></div>
                        <div><dt class="text-sm text-gray-500">Année</dt><dd class="font-medium">{{ $dossier->annee_universitaire }}</dd></div>
                        <div><dt class="text-sm text-gray-500">Niveau</dt><dd class="font-medium">{{ $dossier->niveau_etude }}</dd></div>
                        <div><dt class="text-sm text-gray-500">Filière</dt><dd class="font-medium">{{ $dossier->filiere }}</dd></div>
                        <div><dt class="text-sm text-gray-500">Établissement</dt><dd class="font-medium">{{ $dossier->etablissement }}</dd></div>
                        <div><dt class="text-sm text-gray-500">Moyenne</dt><dd class="font-medium">{{ $dossier->moyenne_generale ?? '-' }}/20</dd></div>
                        <div><dt class="text-sm text-gray-500">Revenu familial</dt><dd class="font-medium">{{ $dossier->revenu_familial ? number_format($dossier->revenu_familial, 0, ',', ' ') . ' DH' : '-' }}</dd></div>
                    </dl>

                    @if($dossier->situation_sociale)
                        <div class="mt-4"><dt class="text-sm text-gray-500">Situation sociale</dt><dd class="mt-1">{{ $dossier->situation_sociale }}</dd></div>
                    @endif
                    @if($dossier->motif_demande)
                        <div class="mt-4"><dt class="text-sm text-gray-500">Motif</dt><dd class="mt-1">{{ $dossier->motif_demande }}</dd></div>
                    @endif
                </div>
            </div>

            <!-- Documents -->
            @if($dossier->documents->isNotEmpty())
                <div class="bg-white shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium mb-4">Documents</h3>
                        <ul class="divide-y divide-gray-200">
                            @foreach($dossier->documents as $doc)
                                <li class="py-3 flex justify-between items-center">
                                    <span class="text-sm"><strong>{{ $doc->type_label }}</strong> - {{ $doc->nom_fichier }}</span>
                                    <a href="{{ Storage::url($doc->chemin) }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 text-sm">Voir</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Messages / Échanges -->
            @if($dossier->messages->count())
            <div class="bg-white shadow-sm sm:rounded-lg mb-6 p-6">
                <h3 class="text-lg font-medium mb-4">Échanges</h3>
                <div class="space-y-3 max-h-60 overflow-y-auto">
                    @foreach($dossier->messages as $msg)
                    <div class="p-3 rounded-lg {{ $msg->user_id === auth()->id() ? 'bg-indigo-50 ml-8' : 'bg-gray-50 mr-8' }}">
                        <div class="flex justify-between">
                            <span class="text-xs font-medium text-gray-600">{{ $msg->user->name }}</span>
                            <span class="text-xs text-gray-400">{{ $msg->created_at->format('d/m H:i') }}</span>
                        </div>
                        <p class="text-sm mt-1">{{ $msg->contenu }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if($dossier->statut === 'en_cours_instruction')
                <!-- Demande de complément -->
                <div class="bg-white shadow-sm sm:rounded-lg mb-6 p-6">
                    <h3 class="text-lg font-medium mb-4">Demander un complément</h3>
                    <form method="POST" action="{{ route('instructeur.dossiers.complement', $dossier) }}">
                        @csrf
                        <textarea name="complement_description" rows="3" required class="w-full rounded-md border-gray-300 text-sm mb-3" placeholder="Décrivez les documents ou informations manquantes..."></textarea>
                        <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 text-sm">Demander le complément</button>
                    </form>
                </div>

                <!-- Donner avis formel -->
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium mb-4">Émettre un avis formel</h3>
                    <form method="POST" action="{{ route('instructeur.dossiers.avis', $dossier) }}">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Avis *</label>
                            <select name="avis" class="mt-1 block w-full rounded-md border-gray-300" required>
                                <option value="">-- Sélectionner --</option>
                                <option value="favorable">Favorable</option>
                                <option value="defavorable">Défavorable</option>
                                <option value="reserve">Réservé (nécessite décision admin)</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Justification *</label>
                            <textarea name="commentaire" rows="4" class="mt-1 block w-full rounded-md border-gray-300" required placeholder="Motivez votre avis..."></textarea>
                        </div>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700" onclick="return confirm('Confirmer votre avis ? Il sera transmis à l\'administration.')">
                            Transmettre l'avis
                        </button>
                    </form>
                </div>
            @endif

            @if($dossier->avis_instructeur)
                <div class="bg-white shadow-sm sm:rounded-lg p-6 mt-6">
                    <h3 class="text-lg font-medium mb-2">Votre avis émis</h3>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="px-3 py-1 rounded-full text-sm font-medium bg-{{ $dossier->avis_color }}-100 text-{{ $dossier->avis_color }}-800">{{ $dossier->avis_label }}</span>
                        <span class="text-sm text-gray-500">le {{ $dossier->date_avis_instructeur->format('d/m/Y') }}</span>
                    </div>
                    <p class="text-gray-700">{{ $dossier->commentaire_instructeur }}</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
