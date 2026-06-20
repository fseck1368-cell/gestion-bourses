<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Gestion des Recours</h2></x-slot>

    <div class="py-12"><div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-4">
            <form method="GET" class="flex gap-2">
                <select name="statut" class="rounded-md border-gray-300 text-sm" onchange="this.form.submit()">
                    <option value="">Tous les statuts</option>
                    <option value="soumis" {{ request('statut') == 'soumis' ? 'selected' : '' }}>Soumis</option>
                    <option value="en_examen" {{ request('statut') == 'en_examen' ? 'selected' : '' }}>En examen</option>
                    <option value="accepte" {{ request('statut') == 'accepte' ? 'selected' : '' }}>Accepté</option>
                    <option value="rejete" {{ request('statut') == 'rejete' ? 'selected' : '' }}>Rejeté</option>
                </select>
            </form>
        </div>

        @if(session('success'))<div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">{{ session('success') }}</div>@endif

        <div class="bg-white shadow-sm sm:rounded-lg"><div class="p-6">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50"><tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Référence</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Étudiant</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dossier</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($recours as $r)
                    <tr>
                        <td class="px-6 py-4 text-sm font-mono">{{ $r->reference }}</td>
                        <td class="px-6 py-4 text-sm">{{ $r->etudiant->name }}</td>
                        <td class="px-6 py-4 text-sm">{{ $r->dossier->reference }}</td>
                        <td class="px-6 py-4 text-sm">{{ $r->date_soumission?->format('d/m/Y') }}</td>
                        <td class="px-6 py-4"><span class="px-2 py-1 text-xs rounded-full bg-{{ $r->statut_color }}-100 text-{{ $r->statut_color }}-800">{{ $r->statut_label }}</span></td>
                        <td class="px-6 py-4 text-sm">
                            <a href="{{ route('admin.recours.show', $r) }}" class="text-indigo-600">Examiner</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">Aucun recours.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">{{ $recours->links() }}</div>
        </div></div>
    </div></div>
</x-app-layout>
