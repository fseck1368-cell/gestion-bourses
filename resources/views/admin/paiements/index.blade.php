<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Gestion des Paiements</h2>
            <a href="{{ route('admin.paiements.create') }}" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">+ Nouveau paiement</a>
        </div>
    </x-slot>

    <div class="py-12"><div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white p-4 rounded-lg shadow-sm">
                <p class="text-sm text-gray-500">Total prévu</p>
                <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['total'], 2, ',', ' ') }} DH</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow-sm">
                <p class="text-sm text-gray-500">Total versé</p>
                <p class="text-2xl font-bold text-green-600">{{ number_format($stats['verse'], 2, ',', ' ') }} DH</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow-sm">
                <p class="text-sm text-gray-500">En attente</p>
                <p class="text-2xl font-bold text-yellow-600">{{ number_format($stats['en_attente'], 2, ',', ' ') }} DH</p>
            </div>
        </div>

        <!-- Filtres -->
        <div class="mb-4">
            <form method="GET" class="flex gap-2">
                <select name="statut" class="rounded-md border-gray-300 text-sm" onchange="this.form.submit()">
                    <option value="">Tous les statuts</option>
                    <option value="en_attente" {{ request('statut') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                    <option value="valide" {{ request('statut') == 'valide' ? 'selected' : '' }}>Validé</option>
                    <option value="verse" {{ request('statut') == 'verse' ? 'selected' : '' }}>Versé</option>
                    <option value="annule" {{ request('statut') == 'annule' ? 'selected' : '' }}>Annulé</option>
                </select>
            </form>
        </div>

        @if(session('success'))<div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">{{ session('success') }}</div>@endif

        <div class="bg-white shadow-sm sm:rounded-lg"><div class="p-6">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50"><tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Référence</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Étudiant</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Montant</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mode</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($paiements as $p)
                    <tr>
                        <td class="px-6 py-4 text-sm font-mono">{{ $p->reference }}</td>
                        <td class="px-6 py-4 text-sm">{{ $p->etudiant->name }}</td>
                        <td class="px-6 py-4 text-sm font-semibold">{{ number_format($p->montant, 2, ',', ' ') }} DH</td>
                        <td class="px-6 py-4 text-sm capitalize">{{ $p->mode_paiement }}</td>
                        <td class="px-6 py-4"><span class="px-2 py-1 text-xs rounded-full bg-{{ $p->statut_color }}-100 text-{{ $p->statut_color }}-800">{{ $p->statut_label }}</span></td>
                        <td class="px-6 py-4 text-sm space-x-1">
                            <a href="{{ route('admin.paiements.show', $p) }}" class="text-indigo-600">Voir</a>
                            @if($p->statut === 'en_attente')
                                <form method="POST" action="{{ route('admin.paiements.valider', $p) }}" class="inline">@csrf<button class="text-blue-600">Valider</button></form>
                            @endif
                            @if($p->statut === 'valide')
                                <form method="POST" action="{{ route('admin.paiements.verser', $p) }}" class="inline">@csrf<button class="text-green-600">Verser</button></form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">Aucun paiement.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">{{ $paiements->links() }}</div>
        </div></div>
    </div></div>
</x-app-layout>
