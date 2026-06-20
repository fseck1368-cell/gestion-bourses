<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Critères d'éligibilité</h2>
            <a href="{{ route('admin.criteres.create') }}" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">+ Nouveau critère</a>
        </div>
    </x-slot>

    <div class="py-12"><div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-4">
            <form method="GET" class="flex gap-2">
                <select name="campagne_id" class="rounded-md border-gray-300 text-sm" onchange="this.form.submit()">
                    <option value="">Toutes les campagnes</option>
                    @foreach($campagnes as $c)
                        <option value="{{ $c->id }}" {{ request('campagne_id') == $c->id ? 'selected' : '' }}>{{ $c->nom }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        @if(session('success'))<div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">{{ session('success') }}</div>@endif

        <div class="bg-white shadow-sm sm:rounded-lg"><div class="p-6">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50"><tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Campagne</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Poids</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Obligatoire</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($criteres as $c)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium">{{ $c->nom }}</td>
                        <td class="px-6 py-4 text-sm">{{ $c->campagne->nom }}</td>
                        <td class="px-6 py-4 text-sm capitalize">{{ $c->type }}</td>
                        <td class="px-6 py-4 text-sm">{{ $c->poids }}</td>
                        <td class="px-6 py-4 text-sm">{{ $c->obligatoire ? 'Oui' : 'Non' }}</td>
                        <td class="px-6 py-4"><span class="px-2 py-1 text-xs rounded-full {{ $c->actif ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">{{ $c->actif ? 'Actif' : 'Inactif' }}</span></td>
                        <td class="px-6 py-4 text-sm space-x-2">
                            <a href="{{ route('admin.criteres.edit', $c) }}" class="text-indigo-600">Modifier</a>
                            <form method="POST" action="{{ route('admin.criteres.toggle', $c) }}" class="inline">@csrf<button class="{{ $c->actif ? 'text-red-600' : 'text-green-600' }}">{{ $c->actif ? 'Désactiver' : 'Activer' }}</button></form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">Aucun critère défini.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">{{ $criteres->links() }}</div>
        </div></div>
    </div></div>
</x-app-layout>
