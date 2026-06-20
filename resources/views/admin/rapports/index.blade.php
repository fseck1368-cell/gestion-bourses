<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Rapports Académiques</h2>
            <a href="{{ route('admin.rapports.create') }}" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">+ Nouveau rapport</a>
        </div>
    </x-slot>

    <div class="py-12"><div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-4">
            <form method="GET" class="flex gap-2">
                <select name="statut_academique" class="rounded-md border-gray-300 text-sm" onchange="this.form.submit()">
                    <option value="">Tous les statuts</option>
                    <option value="bon" {{ request('statut_academique') == 'bon' ? 'selected' : '' }}>Bon</option>
                    <option value="acceptable" {{ request('statut_academique') == 'acceptable' ? 'selected' : '' }}>Acceptable</option>
                    <option value="insuffisant" {{ request('statut_academique') == 'insuffisant' ? 'selected' : '' }}>Insuffisant</option>
                    <option value="exclus" {{ request('statut_academique') == 'exclus' ? 'selected' : '' }}>Exclus</option>
                </select>
            </form>
        </div>

        @if(session('success'))<div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">{{ session('success') }}</div>@endif

        <div class="bg-white shadow-sm sm:rounded-lg"><div class="p-6">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50"><tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Étudiant</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Semestre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Moyenne</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Crédits</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Assiduité</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Renouvellement</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($rapports as $r)
                    <tr>
                        <td class="px-6 py-4 text-sm">{{ $r->etudiant->name }}</td>
                        <td class="px-6 py-4 text-sm">{{ $r->semestre }} ({{ $r->annee_universitaire }})</td>
                        <td class="px-6 py-4 text-sm font-semibold">{{ $r->moyenne ?? '-' }}/20</td>
                        <td class="px-6 py-4 text-sm">{{ $r->credits_valides ?? '-' }}/{{ $r->credits_total ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm">{{ $r->taux_assiduite ?? '-' }}%</td>
                        <td class="px-6 py-4"><span class="px-2 py-1 text-xs rounded-full bg-{{ $r->statut_color }}-100 text-{{ $r->statut_color }}-800">{{ $r->statut_label }}</span></td>
                        <td class="px-6 py-4 text-sm">
                            <span class="{{ $r->renouvellement_recommande ? 'text-green-600' : 'text-red-600' }}">
                                {{ $r->renouvellement_recommande ? 'Recommandé' : 'Non recommandé' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">Aucun rapport.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">{{ $rapports->links() }}</div>
        </div></div>
    </div></div>
</x-app-layout>
