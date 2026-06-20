<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Mes dossiers assignés</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))<div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">{{ session('success') }}</div>@endif

            <!-- Stats instructeur -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-xl p-4 shadow border-l-4 border-blue-500">
                    <p class="text-xs text-gray-500 uppercase">En cours</p>
                    <p class="text-2xl font-bold text-blue-700">{{ $stats['en_cours'] }}</p>
                </div>
                <div class="bg-white rounded-xl p-4 shadow border-l-4 border-green-500">
                    <p class="text-xs text-gray-500 uppercase">Traités ce mois</p>
                    <p class="text-2xl font-bold text-green-700">{{ $stats['traites_mois'] }}</p>
                </div>
                <div class="bg-white rounded-xl p-4 shadow border-l-4 border-yellow-500">
                    <p class="text-xs text-gray-500 uppercase">Compléments en attente</p>
                    <p class="text-2xl font-bold text-yellow-700">{{ $stats['complement_en_attente'] }}</p>
                </div>
                <div class="bg-white rounded-xl p-4 shadow border-l-4 border-indigo-500">
                    <p class="text-xs text-gray-500 uppercase">RDV à venir</p>
                    <p class="text-2xl font-bold text-indigo-700">{{ $stats['rdv_a_venir'] }}</p>
                </div>
            </div>

            <div class="mb-4">
                <a href="{{ route('instructeur.rendez-vous.index') }}" class="text-indigo-600 hover:underline text-sm font-medium">Voir mes rendez-vous &rarr;</a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Référence</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Étudiant</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Filière</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Avis</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Complément</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($dossiers as $dossier)
                                    <tr>
                                        <td class="px-6 py-4 text-sm font-mono font-medium">{{ $dossier->reference }}</td>
                                        <td class="px-6 py-4 text-sm">{{ $dossier->etudiant->name }}</td>
                                        <td class="px-6 py-4 text-sm">{{ $dossier->filiere }}</td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-{{ $dossier->statut_color }}-100 text-{{ $dossier->statut_color }}-800">{{ $dossier->statut_label }}</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($dossier->avis_instructeur)
                                                <span class="px-2 py-1 text-xs rounded-full bg-{{ $dossier->avis_color }}-100 text-{{ $dossier->avis_color }}-800">{{ $dossier->avis_label }}</span>
                                            @else
                                                <span class="text-xs text-gray-400">Non émis</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-xs">
                                            @if($dossier->complement_requis && !$dossier->complement_date_reponse)
                                                <span class="text-yellow-600 font-medium">En attente</span>
                                            @elseif($dossier->complement_requis && $dossier->complement_date_reponse)
                                                <span class="text-green-600 font-medium">Reçu</span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            <a href="{{ route('instructeur.dossiers.show', $dossier) }}" class="text-indigo-600 hover:text-indigo-900">Examiner</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">Aucun dossier assigné.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $dossiers->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
