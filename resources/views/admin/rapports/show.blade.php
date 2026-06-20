<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Rapport Académique - {{ $rapport->etudiant->name }}</h2></x-slot>

    <div class="py-12"><div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm sm:rounded-lg p-6">
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <h3 class="font-semibold text-lg mb-4">Informations</h3>
                    <dl class="space-y-2">
                        <div><dt class="text-sm text-gray-500">Étudiant</dt><dd>{{ $rapport->etudiant->name }}</dd></div>
                        <div><dt class="text-sm text-gray-500">Convention</dt><dd class="font-mono">{{ $rapport->convention?->reference ?? '-' }}</dd></div>
                        <div><dt class="text-sm text-gray-500">Semestre</dt><dd>{{ $rapport->semestre }}</dd></div>
                        <div><dt class="text-sm text-gray-500">Année universitaire</dt><dd>{{ $rapport->annee_universitaire }}</dd></div>
                    </dl>
                </div>
                <div>
                    <h3 class="font-semibold text-lg mb-4">Résultats</h3>
                    <dl class="space-y-2">
                        <div><dt class="text-sm text-gray-500">Moyenne</dt><dd class="text-xl font-bold">{{ $rapport->moyenne ?? '-' }}/20</dd></div>
                        <div><dt class="text-sm text-gray-500">Crédits</dt><dd>{{ $rapport->credits_valides ?? '-' }}/{{ $rapport->credits_total ?? '-' }}</dd></div>
                        <div><dt class="text-sm text-gray-500">Assiduité</dt><dd>{{ $rapport->taux_assiduite ?? '-' }}%</dd></div>
                        <div><dt class="text-sm text-gray-500">Statut</dt><dd><span class="px-2 py-1 text-xs rounded-full bg-{{ $rapport->statut_color }}-100 text-{{ $rapport->statut_color }}-800">{{ $rapport->statut_label }}</span></dd></div>
                        <div><dt class="text-sm text-gray-500">Renouvellement</dt><dd class="{{ $rapport->renouvellement_recommande ? 'text-green-600' : 'text-red-600' }} font-semibold">{{ $rapport->renouvellement_recommande ? 'Recommandé' : 'Non recommandé' }}</dd></div>
                    </dl>
                </div>
            </div>

            @if($rapport->observations)
                <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                    <p class="text-sm font-medium text-gray-700 mb-1">Observations</p>
                    <p class="text-gray-600 whitespace-pre-line">{{ $rapport->observations }}</p>
                </div>
            @endif

            <div class="mt-6">
                <a href="{{ route('admin.rapports.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md">Retour à la liste</a>
            </div>
        </div>
    </div></div>
</x-app-layout>
