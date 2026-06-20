<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-white leading-tight">{{ __('Dashboard') }} - {{ __('Administration') }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Alertes -->
            @if($alertes->count())
            <div class="mb-6 space-y-2">
                @foreach($alertes as $alerte)
                <div class="p-3 rounded-lg border-l-4 border-{{ $alerte->niveau_color }}-500 bg-{{ $alerte->niveau_color }}-50 flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <span class="text-{{ $alerte->niveau_color }}-600 font-medium text-sm">{{ $alerte->titre }}</span>
                        <span class="text-{{ $alerte->niveau_color }}-500 text-sm">— {{ $alerte->message }}</span>
                    </div>
                    @if($alerte->lien)<a href="{{ $alerte->lien }}" class="text-sm text-indigo-600 hover:underline">{{ __('View') }}</a>@endif
                </div>
                @endforeach
            </div>
            @endif

            <!-- Stats dossiers -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
                <div class="bg-white rounded-xl p-5 shadow-lg border-t-4 border-dark-900 text-center">
                    <div class="text-3xl font-extrabold text-dark-900">{{ $stats['total'] }}</div>
                    <div class="text-xs text-dark-500 font-medium mt-1 uppercase tracking-wide">{{ __('Total') }}</div>
                </div>
                <div class="bg-white rounded-xl p-5 shadow-lg border-t-4 border-primary-500 text-center">
                    <div class="text-3xl font-extrabold text-primary-600">{{ $stats['soumis'] }}</div>
                    <div class="text-xs text-dark-500 font-medium mt-1 uppercase tracking-wide">{{ __('Submitted') }}</div>
                </div>
                <div class="bg-white rounded-xl p-5 shadow-lg border-t-4 border-secondary-500 text-center">
                    <div class="text-3xl font-extrabold text-secondary-600">{{ $stats['en_cours'] }}</div>
                    <div class="text-xs text-dark-500 font-medium mt-1 uppercase tracking-wide">{{ __('In progress') }}</div>
                </div>
                <div class="bg-white rounded-xl p-5 shadow-lg border-t-4 border-green-500 text-center">
                    <div class="text-3xl font-extrabold text-green-600">{{ $stats['acceptes'] }}</div>
                    <div class="text-xs text-dark-500 font-medium mt-1 uppercase tracking-wide">{{ __('Accepted') }}</div>
                </div>
                <div class="bg-white rounded-xl p-5 shadow-lg border-t-4 border-red-500 text-center">
                    <div class="text-3xl font-extrabold text-red-600">{{ $stats['rejetes'] }}</div>
                    <div class="text-xs text-dark-500 font-medium mt-1 uppercase tracking-wide">{{ __('Rejected') }}</div>
                </div>
            </div>

            <!-- Stats financières + Taux -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                <div class="bg-white rounded-xl p-4 shadow border-l-4 border-indigo-500">
                    <p class="text-xs text-gray-500 uppercase">{{ __('Budgets') }}</p>
                    <p class="text-xl font-bold text-indigo-700">{{ number_format($statsFinancieres['budget_total'], 0, ',', ' ') }} DH</p>
                </div>
                <div class="bg-white rounded-xl p-4 shadow border-l-4 border-green-500">
                    <p class="text-xs text-gray-500 uppercase">{{ __('Paid') }}</p>
                    <p class="text-xl font-bold text-green-700">{{ number_format($statsFinancieres['total_verse'], 0, ',', ' ') }} DH</p>
                </div>
                <div class="bg-white rounded-xl p-4 shadow border-l-4 border-yellow-500">
                    <p class="text-xs text-gray-500 uppercase">{{ __('Pending') }}</p>
                    <p class="text-xl font-bold text-yellow-700">{{ $statsFinancieres['paiements_en_attente'] }}</p>
                </div>
                <div class="bg-white rounded-xl p-4 shadow border-l-4 border-red-500">
                    <p class="text-xs text-gray-500 uppercase">{{ __('Appeals') }}</p>
                    <p class="text-xl font-bold text-red-700">{{ $recoursEnAttente }}</p>
                </div>
                <div class="bg-white rounded-xl p-4 shadow border-l-4 border-purple-500">
                    <p class="text-xs text-gray-500 uppercase">Taux d'acceptation</p>
                    <p class="text-xl font-bold text-purple-700">{{ $tauxAcceptation }}%</p>
                </div>
            </div>

            <!-- Graphiques -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Graphique dossiers par mois -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h4 class="font-semibold text-dark-800 mb-4">{{ __('Dossiers') }} par mois ({{ now()->year }})</h4>
                    <canvas id="chartDossiersParMois" height="200"></canvas>
                </div>

                <!-- Graphique répartition par statut -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h4 class="font-semibold text-dark-800 mb-4">Répartition par statut</h4>
                    <canvas id="chartStatuts" height="200"></canvas>
                </div>
            </div>

            <!-- Utilisateurs + Budget -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h4 class="font-semibold text-dark-800 mb-4">{{ __('Users') }}</h4>
                    <canvas id="chartUsers" height="200"></canvas>
                </div>
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h4 class="font-semibold text-dark-800 mb-4">Budget : Alloué vs Consommé</h4>
                    <div class="flex items-center justify-center h-48">
                        <div class="w-full">
                            @php $tauxBudget = $statsFinancieres['budget_total'] > 0 ? round(($statsFinancieres['budget_consomme'] / $statsFinancieres['budget_total']) * 100, 1) : 0; @endphp
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-gray-600">Consommé</span>
                                <span class="font-bold text-indigo-700">{{ $tauxBudget }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-6">
                                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 h-6 rounded-full flex items-center justify-end pr-2 transition-all" style="width: {{ min($tauxBudget, 100) }}%">
                                    <span class="text-white text-xs font-bold">{{ number_format($statsFinancieres['budget_consomme'], 0, ',', ' ') }} DH</span>
                                </div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500 mt-2">
                                <span>0 DH</span>
                                <span>{{ number_format($statsFinancieres['budget_total'], 0, ',', ' ') }} DH</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Temps de traitement + Activité récente -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Temps de traitement moyen -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h4 class="font-semibold text-dark-800 mb-4">Performance</h4>
                    <div class="flex items-center gap-6">
                        <div class="text-center">
                            <div class="text-4xl font-extrabold text-indigo-600">{{ $tempsTraitementMoyen }}</div>
                            <div class="text-sm text-gray-500 mt-1">jours en moyenne</div>
                            <div class="text-xs text-gray-400">entre soumission et décision</div>
                        </div>
                        <div class="flex-1 space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">{{ __('Submitted') }}</span>
                                <span class="text-sm font-bold text-primary-600">{{ $stats['soumis'] }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">{{ __('In progress') }}</span>
                                <span class="text-sm font-bold text-secondary-600">{{ $stats['en_cours'] }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Taux acceptation</span>
                                <span class="text-sm font-bold text-green-600">{{ $tauxAcceptation }}%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Fil d'activité récente -->
                <div class="bg-white rounded-xl shadow-lg p-6 max-h-80 overflow-y-auto">
                    <h4 class="font-semibold text-dark-800 mb-4">Activité récente</h4>
                    <div class="space-y-3">
                        @foreach($activitesRecentes as $activite)
                        <div class="flex items-start gap-3">
                            <div class="w-2 h-2 mt-2 rounded-full bg-indigo-500 flex-shrink-0"></div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-dark-700 truncate">
                                    <span class="font-medium">{{ $activite->user?->prenom ?? 'Système' }}</span> — {{ $activite->action }}
                                </p>
                                <p class="text-xs text-gray-400">{{ $activite->dossier?->reference }} · {{ $activite->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="flex flex-wrap gap-3 mb-6">
                <a href="{{ route('admin.export.rapport') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-dark-900 text-white text-sm font-medium rounded-lg hover:bg-dark-800 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    {{ __('Export PDF') }}
                </a>
                <a href="{{ route('admin.export.csv') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-secondary-600 text-white text-sm font-medium rounded-lg hover:bg-secondary-700 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    {{ __('Export CSV') }}
                </a>
                <a href="{{ route('admin.statistiques') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-500 text-dark-900 text-sm font-bold rounded-lg hover:bg-primary-600 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    {{ __('Statistics') }}
                </a>
                <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                    Ajouter utilisateur
                </a>
            </div>

            <!-- Derniers dossiers -->
            <div class="bg-white rounded-xl shadow-lg border border-dark-200 overflow-hidden">
                <div class="px-6 py-4 bg-dark-900 flex justify-between items-center">
                    <h3 class="text-white font-semibold">{{ __('Dossiers') }} récents</h3>
                    <a href="{{ route('admin.dossiers.index') }}" class="text-primary-400 hover:text-primary-300 text-sm font-medium">Voir tous &rarr;</a>
                </div>
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b border-dark-200">
                                    <th class="px-4 py-3 text-left text-xs font-bold text-dark-600 uppercase tracking-wider">{{ __('Reference') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-dark-600 uppercase tracking-wider">{{ __('Name') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-dark-600 uppercase tracking-wider">{{ __('Status') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-dark-600 uppercase tracking-wider">{{ __('Date') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-dark-600 uppercase tracking-wider">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-dark-100">
                                @foreach($derniersDossiers as $dossier)
                                    <tr class="hover:bg-primary-50/50 transition">
                                        <td class="px-4 py-3 text-sm font-mono font-semibold text-secondary-700">{{ $dossier->reference }}</td>
                                        <td class="px-4 py-3 text-sm text-dark-700">{{ $dossier->etudiant->name }}</td>
                                        <td class="px-4 py-3">
                                            @php $colors = ['soumis' => 'bg-primary-100 text-primary-800 border-primary-300', 'en_cours_instruction' => 'bg-secondary-100 text-secondary-800 border-secondary-300', 'accepte' => 'bg-green-100 text-green-800 border-green-300', 'rejete' => 'bg-red-100 text-red-800 border-red-300']; @endphp
                                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full border {{ $colors[$dossier->statut] ?? '' }}">{{ $dossier->statut_label }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-dark-500">{{ $dossier->created_at->format('d/m/Y') }}</td>
                                        <td class="px-4 py-3"><a href="{{ route('admin.dossiers.show', $dossier) }}" class="px-3 py-1 bg-dark-900 text-white text-xs font-medium rounded-md hover:bg-dark-700 transition">{{ __('View') }}</a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const moisLabels = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
        const dossiersData = @json($dossiersParMois);
        const dossiersParMoisValues = moisLabels.map((_, i) => dossiersData[String(i+1).padStart(2, '0')] || 0);

        new Chart(document.getElementById('chartDossiersParMois'), {
            type: 'bar',
            data: {
                labels: moisLabels,
                datasets: [{
                    label: 'Dossiers',
                    data: dossiersParMoisValues,
                    backgroundColor: 'rgba(99, 102, 241, 0.7)',
                    borderColor: 'rgba(99, 102, 241, 1)',
                    borderWidth: 1,
                    borderRadius: 6
                }]
            },
            options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
        });

        new Chart(document.getElementById('chartStatuts'), {
            type: 'doughnut',
            data: {
                labels: ['Soumis', 'En cours', 'Acceptés', 'Rejetés'],
                datasets: [{
                    data: [{{ $stats['soumis'] }}, {{ $stats['en_cours'] }}, {{ $stats['acceptes'] }}, {{ $stats['rejetes'] }}],
                    backgroundColor: ['#f59e0b', '#6366f1', '#10b981', '#ef4444']
                }]
            },
            options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
        });

        new Chart(document.getElementById('chartUsers'), {
            type: 'pie',
            data: {
                labels: ['Étudiants', 'Instructeurs', 'Admins'],
                datasets: [{
                    data: [{{ $statsUtilisateurs['etudiants'] }}, {{ $statsUtilisateurs['instructeurs'] }}, {{ $statsUtilisateurs['admins'] }}],
                    backgroundColor: ['#3b82f6', '#8b5cf6', '#f97316']
                }]
            },
            options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
        });
    </script>
    @endpush
</x-app-layout>
