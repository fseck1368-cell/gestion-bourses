<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Conventions</h2>
            <a href="{{ route('admin.conventions.create') }}" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">+ Nouvelle convention</a>
        </div>
    </x-slot>

    <div class="py-12"><div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-4">
            <form method="GET" class="flex gap-2">
                <select name="statut" class="rounded-md border-gray-300 text-sm" onchange="this.form.submit()">
                    <option value="">Tous les statuts</option>
                    <option value="brouillon" {{ request('statut') == 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                    <option value="active" {{ request('statut') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="suspendue" {{ request('statut') == 'suspendue' ? 'selected' : '' }}>Suspendue</option>
                    <option value="terminee" {{ request('statut') == 'terminee' ? 'selected' : '' }}>Terminée</option>
                    <option value="resiliee" {{ request('statut') == 'resiliee' ? 'selected' : '' }}>Résiliée</option>
                </select>
            </form>
        </div>

        @if(session('success'))<div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">{{ session('success') }}</div>@endif

        <div class="bg-white shadow-sm sm:rounded-lg"><div class="p-6">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50"><tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Référence</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Étudiant</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Montant/mois</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durée</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Période</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($conventions as $c)
                    <tr>
                        <td class="px-6 py-4 text-sm font-mono">{{ $c->reference }}</td>
                        <td class="px-6 py-4 text-sm">{{ $c->etudiant->name }}</td>
                        <td class="px-6 py-4 text-sm">{{ number_format($c->montant_mensuel, 2, ',', ' ') }} DH</td>
                        <td class="px-6 py-4 text-sm">{{ $c->duree_mois }} mois</td>
                        <td class="px-6 py-4 text-sm">{{ $c->date_debut->format('d/m/Y') }} - {{ $c->date_fin->format('d/m/Y') }}</td>
                        <td class="px-6 py-4"><span class="px-2 py-1 text-xs rounded-full bg-{{ $c->statut_color }}-100 text-{{ $c->statut_color }}-800">{{ $c->statut_label }}</span></td>
                        <td class="px-6 py-4 text-sm">
                            <a href="{{ route('admin.conventions.show', $c) }}" class="text-indigo-600">Voir</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">Aucune convention.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">{{ $conventions->links() }}</div>
        </div></div>
    </div></div>
</x-app-layout>
