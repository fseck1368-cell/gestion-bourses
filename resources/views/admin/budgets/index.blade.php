<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Gestion des Budgets</h2>
            <a href="{{ route('admin.budgets.create') }}" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">+ Nouveau budget</a>
        </div>
    </x-slot>

    <div class="py-12"><div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Stats globales -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white p-4 rounded-lg shadow-sm">
                <p class="text-sm text-gray-500">Budget total alloué</p>
                <p class="text-2xl font-bold text-gray-800">{{ number_format($totalAlloue, 2, ',', ' ') }} DH</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow-sm">
                <p class="text-sm text-gray-500">Total consommé</p>
                <p class="text-2xl font-bold text-red-600">{{ number_format($totalConsomme, 2, ',', ' ') }} DH</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow-sm">
                <p class="text-sm text-gray-500">Restant</p>
                <p class="text-2xl font-bold text-green-600">{{ number_format($totalAlloue - $totalConsomme, 2, ',', ' ') }} DH</p>
            </div>
        </div>

        @if(session('success'))<div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">{{ session('success') }}</div>@endif

        <div class="bg-white shadow-sm sm:rounded-lg"><div class="p-6">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50"><tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Libellé</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Campagne</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Alloué</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Consommé</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Taux</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($budgets as $b)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium">{{ $b->libelle }}</td>
                        <td class="px-6 py-4 text-sm">{{ $b->campagne->nom }}</td>
                        <td class="px-6 py-4 text-sm">{{ number_format($b->montant_alloue, 2, ',', ' ') }} DH</td>
                        <td class="px-6 py-4 text-sm">{{ number_format($b->montant_consomme, 2, ',', ' ') }} DH</td>
                        <td class="px-6 py-4 text-sm">
                            <div class="flex items-center gap-2">
                                <div class="w-20 bg-gray-200 rounded-full h-2">
                                    <div class="h-2 rounded-full {{ $b->taux_consommation > 80 ? 'bg-red-500' : ($b->taux_consommation > 50 ? 'bg-yellow-500' : 'bg-green-500') }}" style="width: {{ min($b->taux_consommation, 100) }}%"></div>
                                </div>
                                <span class="text-xs">{{ $b->taux_consommation }}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <a href="{{ route('admin.budgets.edit', $b) }}" class="text-indigo-600">Modifier</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">Aucun budget.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">{{ $budgets->links() }}</div>
        </div></div>
    </div></div>
</x-app-layout>
