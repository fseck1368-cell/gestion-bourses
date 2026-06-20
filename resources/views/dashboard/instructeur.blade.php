<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-white leading-tight">{{ __('Dashboard') }} - Instructeur</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- KPIs -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-xl p-5 shadow-lg border-t-4 border-yellow-500 text-center">
                    <div class="text-3xl font-extrabold text-yellow-600">{{ $stats['assignes'] }}</div>
                    <div class="text-xs text-dark-500 font-medium mt-1 uppercase tracking-wide">{{ __('Pending') }}</div>
                </div>
                <div class="bg-white rounded-xl p-5 shadow-lg border-t-4 border-green-500 text-center">
                    <div class="text-3xl font-extrabold text-green-600">{{ $stats['total_traites'] }}</div>
                    <div class="text-xs text-dark-500 font-medium mt-1 uppercase tracking-wide">Total traités</div>
                </div>
                <div class="bg-white rounded-xl p-5 shadow-lg border-t-4 border-blue-500 text-center">
                    <div class="text-3xl font-extrabold text-blue-600">{{ $stats['traites_semaine'] }}</div>
                    <div class="text-xs text-dark-500 font-medium mt-1 uppercase tracking-wide">Cette semaine</div>
                </div>
                <div class="bg-white rounded-xl p-5 shadow-lg border-t-4 border-red-500 text-center">
                    <div class="text-3xl font-extrabold text-red-600">{{ $dossiersEnRetard }}</div>
                    <div class="text-xs text-dark-500 font-medium mt-1 uppercase tracking-wide">En retard (+7j)</div>
                </div>
            </div>

            <!-- Alerte retard -->
            @if($dossiersEnRetard > 0)
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>
                    <span class="text-red-800 font-medium">{{ $dossiersEnRetard }} dossier(s) en attente depuis plus de 7 jours !</span>
                </div>
            </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Graphique Avis -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h4 class="font-semibold text-dark-800 mb-4">Mes avis émis</h4>
                    <canvas id="chartAvis" height="200"></canvas>
                </div>

                <!-- Rendez-vous à venir -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h4 class="font-semibold text-dark-800 mb-4">{{ __('Appointments') }} à venir</h4>
                    @if($rdvAVenir->isEmpty())
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <p class="text-gray-500">Aucun rendez-vous à venir</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($rdvAVenir as $rdv)
                            <div class="flex items-center justify-between p-3 rounded-lg border {{ $rdv->statut === 'confirme' ? 'border-green-200 bg-green-50' : 'border-yellow-200 bg-yellow-50' }}">
                                <div>
                                    <p class="font-medium text-dark-800">{{ $rdv->etudiant->prenom }} {{ $rdv->etudiant->nom }}</p>
                                    <p class="text-sm text-gray-500">{{ $rdv->date_heure->format('d/m/Y H:i') }} {{ $rdv->lieu ? '- ' . $rdv->lieu : '' }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $rdv->statut === 'confirme' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $rdv->statut === 'confirme' ? 'Confirmé' : 'En attente' }}
                                </span>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Dossiers à instruire -->
            <div class="bg-white rounded-xl shadow-lg border border-dark-200 overflow-hidden mb-6">
                <div class="px-6 py-4 bg-secondary-700 flex justify-between items-center">
                    <h3 class="text-white font-semibold">{{ __('Dossiers') }} à instruire ({{ $dossiersAssignes->count() }})</h3>
                    <a href="{{ route('instructeur.dossiers.index') }}" class="text-primary-300 hover:text-primary-200 text-sm">Voir tous &rarr;</a>
                </div>
                <div class="p-6">
                    @if($dossiersAssignes->isEmpty())
                        <div class="text-center py-8">
                            <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-7 h-7 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <p class="text-dark-500">Tous les dossiers sont traités !</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($dossiersAssignes as $dossier)
                                <div class="flex items-center justify-between p-4 rounded-lg border border-dark-200 hover:border-primary-300 hover:bg-primary-50/30 transition">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 bg-secondary-100 rounded-full flex items-center justify-center">
                                            <span class="text-secondary-700 font-bold text-sm">{{ strtoupper(substr($dossier->etudiant->prenom, 0, 1)) }}{{ strtoupper(substr($dossier->etudiant->nom, 0, 1)) }}</span>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-dark-800">{{ $dossier->etudiant->prenom }} {{ $dossier->etudiant->nom }}</p>
                                            <p class="text-sm text-dark-500">{{ $dossier->reference }} | {{ $dossier->filiere }} | {{ $dossier->niveau_etude }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        @if($dossier->date_instruction && $dossier->date_instruction->diffInDays(now()) > 7)
                                            <span class="px-2 py-1 text-xs bg-red-100 text-red-700 rounded-full font-medium">{{ $dossier->date_instruction->diffInDays(now()) }}j</span>
                                        @endif
                                        <a href="{{ route('instructeur.dossiers.show', $dossier) }}" class="px-4 py-2 bg-primary-500 text-dark-900 text-sm font-bold rounded-lg hover:bg-primary-600 transition">
                                            Examiner
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Derniers dossiers traités -->
            <div class="bg-white rounded-xl shadow-lg border border-dark-200 overflow-hidden">
                <div class="px-6 py-4 bg-dark-900">
                    <h3 class="text-white font-semibold">Derniers dossiers traités</h3>
                </div>
                <div class="p-6">
                    @if($dossiersTraites->isEmpty())
                        <p class="text-dark-500 text-center py-4">Aucun dossier traité.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead><tr class="border-b border-dark-200">
                                    <th class="px-4 py-3 text-left text-xs font-bold text-dark-600 uppercase">{{ __('Reference') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-dark-600 uppercase">{{ __('Name') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-dark-600 uppercase">Avis</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-dark-600 uppercase">{{ __('Status') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-dark-600 uppercase">{{ __('Date') }}</th>
                                </tr></thead>
                                <tbody class="divide-y divide-dark-100">
                                    @foreach($dossiersTraites as $dossier)
                                        <tr class="hover:bg-dark-50 transition">
                                            <td class="px-4 py-3 text-sm font-mono text-secondary-700">{{ $dossier->reference }}</td>
                                            <td class="px-4 py-3 text-sm">{{ $dossier->etudiant->prenom }} {{ $dossier->etudiant->nom }}</td>
                                            <td class="px-4 py-3">
                                                @if($dossier->avis_instructeur)
                                                    @php $avisColors = ['favorable' => 'bg-green-100 text-green-800', 'defavorable' => 'bg-red-100 text-red-800', 'reserve' => 'bg-yellow-100 text-yellow-800']; @endphp
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $avisColors[$dossier->avis_instructeur] ?? '' }}">{{ ucfirst($dossier->avis_instructeur) }}</span>
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3">
                                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $dossier->statut === 'accepte' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ $dossier->statut_label }}</span>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-dark-500">{{ $dossier->date_decision?->format('d/m/Y') }}</td>
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
        new Chart(document.getElementById('chartAvis'), {
            type: 'doughnut',
            data: {
                labels: ['Favorable', 'Défavorable', 'Réservé'],
                datasets: [{
                    data: [{{ $stats['favorables'] }}, {{ $stats['defavorables'] }}, {{ $stats['total_traites'] - $stats['favorables'] - $stats['defavorables'] }}],
                    backgroundColor: ['#10b981', '#ef4444', '#f59e0b']
                }]
            },
            options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
        });
    </script>
    @endpush
</x-app-layout>
