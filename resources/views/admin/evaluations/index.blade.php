<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Évaluations des dossiers</h2></x-slot>

    <div class="py-12"><div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if(session('success'))<div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">{{ session('success') }}</div>@endif

        <div class="bg-white shadow-sm sm:rounded-lg"><div class="p-6">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50"><tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dossier</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Étudiant</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Campagne</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Score</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Évaluations</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($dossiers as $d)
                    <tr>
                        <td class="px-6 py-4 text-sm font-mono">{{ $d->reference }}</td>
                        <td class="px-6 py-4 text-sm">{{ $d->etudiant->name }}</td>
                        <td class="px-6 py-4 text-sm">{{ $d->campagne?->nom ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm font-semibold">{{ $d->score_global ? $d->score_global . '/20' : 'Non évalué' }}</td>
                        <td class="px-6 py-4 text-sm">{{ $d->evaluations->count() }} critère(s)</td>
                        <td class="px-6 py-4 text-sm">
                            <a href="{{ route('admin.evaluations.evaluer', $d) }}" class="text-indigo-600 hover:underline">Évaluer</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">Aucun dossier en cours d'instruction.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">{{ $dossiers->links() }}</div>
        </div></div>
    </div></div>
</x-app-layout>
