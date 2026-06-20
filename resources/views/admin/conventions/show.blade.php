<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Convention {{ $convention->reference }}</h2></x-slot>

    <div class="py-12"><div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        @if(session('success'))<div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">{{ session('success') }}</div>@endif

        <div class="bg-white shadow-sm sm:rounded-lg p-6 mb-6">
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <h3 class="font-semibold text-lg mb-4">Détails de la convention</h3>
                    <dl class="space-y-2">
                        <div><dt class="text-sm text-gray-500">Référence</dt><dd class="font-mono">{{ $convention->reference }}</dd></div>
                        <div><dt class="text-sm text-gray-500">Étudiant</dt><dd>{{ $convention->etudiant->name }}</dd></div>
                        <div><dt class="text-sm text-gray-500">Dossier</dt><dd>{{ $convention->dossier->reference }}</dd></div>
                        <div><dt class="text-sm text-gray-500">Statut</dt><dd><span class="px-2 py-1 text-xs rounded-full bg-{{ $convention->statut_color }}-100 text-{{ $convention->statut_color }}-800">{{ $convention->statut_label }}</span></dd></div>
                    </dl>
                </div>
                <div>
                    <h3 class="font-semibold text-lg mb-4">Financier</h3>
                    <dl class="space-y-2">
                        <div><dt class="text-sm text-gray-500">Montant mensuel</dt><dd class="font-bold">{{ number_format($convention->montant_mensuel, 2, ',', ' ') }} DH</dd></div>
                        <div><dt class="text-sm text-gray-500">Durée</dt><dd>{{ $convention->duree_mois }} mois</dd></div>
                        <div><dt class="text-sm text-gray-500">Montant total</dt><dd class="text-lg font-bold text-indigo-600">{{ number_format($convention->montant_total, 2, ',', ' ') }} DH</dd></div>
                        <div><dt class="text-sm text-gray-500">Période</dt><dd>{{ $convention->date_debut->format('d/m/Y') }} - {{ $convention->date_fin->format('d/m/Y') }}</dd></div>
                        <div><dt class="text-sm text-gray-500">Date signature</dt><dd>{{ $convention->date_signature?->format('d/m/Y') ?? 'Non signée' }}</dd></div>
                    </dl>
                </div>
            </div>

            @if($convention->conditions)
                <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                    <p class="text-sm font-medium text-gray-700 mb-1">Conditions</p>
                    <p class="text-gray-600 whitespace-pre-line">{{ $convention->conditions }}</p>
                </div>
            @endif

            @if($convention->obligations_etudiant)
                <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                    <p class="text-sm font-medium text-gray-700 mb-1">Obligations de l'étudiant</p>
                    <p class="text-gray-600 whitespace-pre-line">{{ $convention->obligations_etudiant }}</p>
                </div>
            @endif

            <!-- Actions -->
            <div class="mt-6 flex gap-2">
                @if($convention->statut === 'brouillon')
                    <form method="POST" action="{{ route('admin.conventions.activer', $convention) }}">@csrf<button class="px-4 py-2 bg-green-600 text-white rounded-md">Activer</button></form>
                @endif
                @if($convention->statut === 'active')
                    <form method="POST" action="{{ route('admin.conventions.suspendre', $convention) }}">@csrf<button class="px-4 py-2 bg-yellow-600 text-white rounded-md">Suspendre</button></form>
                @endif
                @if(in_array($convention->statut, ['active', 'suspendue']))
                    <button onclick="document.getElementById('form-resilier').classList.toggle('hidden')" class="px-4 py-2 bg-red-600 text-white rounded-md">Résilier</button>
                @endif
            </div>

            @if(in_array($convention->statut, ['active', 'suspendue']))
            <form id="form-resilier" method="POST" action="{{ route('admin.conventions.resilier', $convention) }}" class="hidden mt-4 p-4 bg-red-50 rounded-lg">
                @csrf
                <label class="block text-sm font-medium text-gray-700 mb-1">Motif de résiliation</label>
                <textarea name="motif_resiliation" rows="3" required class="w-full rounded-md border-gray-300 mb-2"></textarea>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md">Confirmer la résiliation</button>
            </form>
            @endif
        </div>

        <!-- Rapports académiques liés -->
        @if($convention->rapportsAcademiques->count())
        <div class="bg-white shadow-sm sm:rounded-lg p-6">
            <h3 class="font-semibold text-lg mb-4">Rapports académiques</h3>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50"><tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Semestre</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Moyenne</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Assiduité</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Statut</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Renouvellement</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($convention->rapportsAcademiques as $r)
                    <tr>
                        <td class="px-4 py-2 text-sm">{{ $r->semestre }}</td>
                        <td class="px-4 py-2 text-sm">{{ $r->moyenne ?? '-' }}/20</td>
                        <td class="px-4 py-2 text-sm">{{ $r->taux_assiduite ?? '-' }}%</td>
                        <td class="px-4 py-2"><span class="px-2 py-1 text-xs rounded-full bg-{{ $r->statut_color }}-100 text-{{ $r->statut_color }}-800">{{ $r->statut_label }}</span></td>
                        <td class="px-4 py-2 text-sm">{{ $r->renouvellement_recommande ? 'Oui' : 'Non' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div></div>
</x-app-layout>
