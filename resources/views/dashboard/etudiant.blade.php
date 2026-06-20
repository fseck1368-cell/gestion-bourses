<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-white leading-tight">{{ __('Dashboard') }} - {{ __('Student area') }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-semibold text-dark-900">{{ __('Welcome') }}, {{ auth()->user()->prenom }} !</h3>
                    <p class="text-dark-500 text-sm">Gérez vos demandes de bourses universitaires</p>
                </div>
                <a href="{{ route('etudiant.dossiers.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary-500 hover:bg-primary-600 text-dark-900 font-bold rounded-lg shadow-lg hover:shadow-xl transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    {{ __('New request') }}
                </a>
            </div>

            <!-- Profil complétude -->
            @php $completion = auth()->user()->profil_completion; @endphp
            @if($completion < 100)
            <div class="bg-white rounded-xl shadow p-4 mb-6 border border-yellow-200">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Complétude du profil</span>
                    <span class="text-sm font-bold text-yellow-700">{{ $completion }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="bg-gradient-to-r from-yellow-400 to-green-500 h-3 rounded-full transition-all" style="width: {{ $completion }}%"></div>
                </div>
                <p class="text-xs text-gray-500 mt-2">
                    @if(!auth()->user()->telephone) Ajoutez votre téléphone. @endif
                    @if(!auth()->user()->numero_etudiant) Ajoutez votre numéro étudiant. @endif
                    <a href="{{ route('profile.edit') }}" class="text-indigo-600 hover:underline font-medium">Compléter mon profil</a>
                </p>
            </div>
            @endif

            <!-- KPIs étudiant -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-xl p-4 shadow border-l-4 border-indigo-500">
                    <p class="text-xs text-gray-500 uppercase">{{ __('My files') }}</p>
                    <p class="text-2xl font-bold text-indigo-700">{{ $dossiers->count() }}</p>
                </div>
                <div class="bg-white rounded-xl p-4 shadow border-l-4 border-green-500">
                    <p class="text-xs text-gray-500 uppercase">Total reçu</p>
                    <p class="text-2xl font-bold text-green-700">{{ number_format($totalVerse, 0, ',', ' ') }} DH</p>
                </div>
                <div class="bg-white rounded-xl p-4 shadow border-l-4 border-blue-500">
                    <p class="text-xs text-gray-500 uppercase">Convention</p>
                    <p class="text-lg font-bold text-blue-700">{{ $convention ? $convention->statut_label : 'Aucune' }}</p>
                </div>
                <div class="bg-white rounded-xl p-4 shadow border-l-4 border-yellow-500">
                    <p class="text-xs text-gray-500 uppercase">Dernier recours</p>
                    <p class="text-lg font-bold text-yellow-700">{{ $recours ? $recours->statut_label : '-' }}</p>
                </div>
            </div>

            <!-- Suivi visuel du dernier dossier -->
            @if($dossiers->isNotEmpty())
            @php $dernierDossier = $dossiers->first(); @endphp
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <h4 class="font-semibold text-dark-800 mb-4">Suivi de votre dernier dossier : <span class="text-secondary-700 font-mono">{{ $dernierDossier->reference }}</span></h4>
                <div class="flex items-center justify-between">
                    @php
                        $etapes = [
                            ['label' => 'Soumis', 'statut' => 'soumis', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                            ['label' => 'En instruction', 'statut' => 'en_cours_instruction', 'icon' => 'M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z'],
                            ['label' => 'Décision', 'statut' => 'decision', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                        ];
                        $statutIndex = match($dernierDossier->statut) {
                            'soumis' => 0,
                            'en_cours_instruction' => 1,
                            'accepte', 'rejete' => 2,
                            default => 0
                        };
                    @endphp
                    @foreach($etapes as $i => $etape)
                        <div class="flex flex-col items-center flex-1">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center {{ $i <= $statutIndex ? ($dernierDossier->statut === 'rejete' && $i === 2 ? 'bg-red-500' : 'bg-green-500') : 'bg-gray-200' }} transition-all">
                                <svg class="w-6 h-6 {{ $i <= $statutIndex ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $etape['icon'] }}"></path></svg>
                            </div>
                            <span class="text-xs font-medium mt-2 {{ $i <= $statutIndex ? 'text-dark-800' : 'text-gray-400' }}">{{ $etape['label'] }}</span>
                            @if($i === $statutIndex)
                                <span class="text-xs mt-1 px-2 py-0.5 rounded-full {{ $dernierDossier->statut === 'rejete' ? 'bg-red-100 text-red-700' : ($dernierDossier->statut === 'accepte' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700') }}">
                                    {{ $dernierDossier->statut_label }}
                                </span>
                            @endif
                        </div>
                        @if($i < count($etapes) - 1)
                            <div class="flex-1 h-1 mx-2 rounded {{ $i < $statutIndex ? 'bg-green-500' : 'bg-gray-200' }}"></div>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Convention active -->
            @if($convention)
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-5 shadow mb-6 border border-blue-200">
                <div class="flex justify-between items-center">
                    <div>
                        <h4 class="font-semibold text-blue-800">Convention active : {{ $convention->reference }}</h4>
                        <p class="text-sm text-blue-600">{{ number_format($convention->montant_mensuel, 0, ',', ' ') }} DH/mois | {{ $convention->date_debut->format('d/m/Y') }} - {{ $convention->date_fin->format('d/m/Y') }}</p>
                    </div>
                    <span class="px-3 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">{{ __('Active') }}</span>
                </div>
            </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Graphique paiements -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h4 class="font-semibold text-dark-800 mb-4">{{ __('Payments') }} reçus ({{ now()->year }})</h4>
                    <canvas id="chartPaiements" height="200"></canvas>
                </div>

                <!-- Rendez-vous à venir -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h4 class="font-semibold text-dark-800 mb-4">{{ __('Appointments') }} à venir</h4>
                    @if($rdvProchains->isEmpty())
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <p class="text-gray-500">Aucun rendez-vous à venir</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($rdvProchains as $rdv)
                            <div class="p-3 rounded-lg border {{ $rdv->statut === 'confirme' ? 'border-green-200 bg-green-50' : 'border-yellow-200 bg-yellow-50' }}">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="font-medium text-dark-800">{{ $rdv->motif }}</p>
                                        <p class="text-sm text-gray-500">{{ $rdv->date_heure->format('d/m/Y à H:i') }} {{ $rdv->lieu ? '| ' . $rdv->lieu : '' }}</p>
                                    </div>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $rdv->statut === 'confirme' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $rdv->statut === 'confirme' ? 'Confirmé' : 'En attente' }}
                                    </span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Paiements récents -->
            @if($paiements->count())
            <div class="bg-white rounded-xl shadow-lg border border-dark-200 overflow-hidden mb-6">
                <div class="px-6 py-4 bg-dark-900 flex justify-between items-center">
                    <h3 class="text-white font-semibold">{{ __('Payments') }} récents</h3>
                    <a href="{{ route('etudiant.releve-paiements') }}" class="text-primary-400 hover:text-primary-300 text-sm">{{ __('Download') }} relevé PDF</a>
                </div>
                <div class="p-4">
                    <div class="space-y-2">
                        @foreach($paiements as $p)
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <div>
                                <span class="font-mono text-sm font-semibold">{{ $p->reference }}</span>
                                <span class="text-gray-500 text-sm ml-2">{{ $p->periode ?? '' }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="font-semibold">{{ number_format($p->montant, 0, ',', ' ') }} DH</span>
                                @php $pColors = ['en_attente' => 'yellow', 'valide' => 'blue', 'verse' => 'green', 'annule' => 'red']; @endphp
                                <span class="px-2 py-0.5 text-xs rounded-full bg-{{ $pColors[$p->statut] ?? 'gray' }}-100 text-{{ $pColors[$p->statut] ?? 'gray' }}-800">{{ $p->statut_label }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Mes dossiers -->
            <div class="bg-white rounded-xl shadow-lg border border-dark-200 overflow-hidden">
                <div class="px-6 py-4 bg-dark-900 border-b border-dark-700">
                    <h3 class="text-white font-semibold">{{ __('My files') }}</h3>
                </div>
                <div class="p-6">
                    @if($dossiers->isEmpty())
                        <div class="text-center py-12">
                            <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <p class="text-dark-500 mb-4">Vous n'avez soumis aucune demande pour le moment.</p>
                            <a href="{{ route('etudiant.dossiers.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary-500 hover:bg-primary-600 text-dark-900 font-bold rounded-lg transition">
                                {{ __('New request') }}
                            </a>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr class="border-b border-dark-200">
                                        <th class="px-4 py-3 text-left text-xs font-bold text-dark-600 uppercase">{{ __('Reference') }}</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-dark-600 uppercase">{{ __('University year') }}</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-dark-600 uppercase">{{ __('Field of study') }}</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-dark-600 uppercase">{{ __('Status') }}</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-dark-600 uppercase">{{ __('Date') }}</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-dark-600 uppercase">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-dark-100">
                                    @foreach($dossiers as $dossier)
                                        <tr class="hover:bg-primary-50/50 transition">
                                            <td class="px-4 py-3 text-sm font-mono font-semibold text-secondary-700">{{ $dossier->reference }}</td>
                                            <td class="px-4 py-3 text-sm text-dark-600">{{ $dossier->annee_universitaire }}</td>
                                            <td class="px-4 py-3 text-sm text-dark-600">{{ $dossier->filiere }}</td>
                                            <td class="px-4 py-3">
                                                @php $colors = ['soumis' => 'bg-primary-100 text-primary-800 border-primary-300', 'en_cours_instruction' => 'bg-secondary-100 text-secondary-800 border-secondary-300', 'accepte' => 'bg-green-100 text-green-800 border-green-300', 'rejete' => 'bg-red-100 text-red-800 border-red-300']; @endphp
                                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full border {{ $colors[$dossier->statut] ?? 'bg-dark-100 text-dark-800' }}">{{ $dossier->statut_label }}</span>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-dark-500">{{ $dossier->created_at->format('d/m/Y') }}</td>
                                            <td class="px-4 py-3 text-sm space-x-2">
                                                <a href="{{ route('etudiant.dossiers.show', $dossier) }}" class="text-secondary-600 hover:text-secondary-800 font-medium">{{ __('View') }}</a>
                                                @if($dossier->estModifiable())
                                                    <a href="{{ route('etudiant.dossiers.edit', $dossier) }}" class="text-primary-600 hover:text-primary-800 font-medium">{{ __('Edit') }}</a>
                                                @endif
                                                @if($dossier->statut === 'rejete')
                                                    <a href="{{ route('etudiant.recours.create', $dossier) }}" class="text-red-600 hover:text-red-800 font-medium">{{ __('Appeals') }}</a>
                                                @endif
                                                <a href="{{ route('etudiant.dossiers.recepisse', $dossier) }}" class="text-dark-500 hover:text-dark-700" title="Récépissé">PDF</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const moisLabels = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
        const paiementsData = @json($paiementsParMois);
        const paiementsValues = moisLabels.map((_, i) => paiementsData[String(i+1).padStart(2, '0')] || 0);

        new Chart(document.getElementById('chartPaiements'), {
            type: 'bar',
            data: {
                labels: moisLabels,
                datasets: [{
                    label: 'Montant (DH)',
                    data: paiementsValues,
                    backgroundColor: 'rgba(16, 185, 129, 0.7)',
                    borderColor: 'rgba(16, 185, 129, 1)',
                    borderWidth: 1,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    </script>
    @endpush
</x-app-layout>
