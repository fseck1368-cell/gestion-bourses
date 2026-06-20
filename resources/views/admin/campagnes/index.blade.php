<x-app-layout>
    <x-slot name="header"><div class="flex justify-between items-center"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Campagnes de bourses</h2><a href="{{ route('admin.campagnes.create') }}" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">+ Nouvelle campagne</a></div></x-slot>
    <div class="py-12"><div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if(session('success'))<div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">{{ session('success') }}</div>@endif
        <div class="bg-white shadow-sm sm:rounded-lg"><div class="p-6"><table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50"><tr><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Année</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ouverture</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Clôture</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th></tr></thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($campagnes as $c)<tr>
                    <td class="px-6 py-4 text-sm font-medium">{{ $c->nom }}</td>
                    <td class="px-6 py-4 text-sm">{{ $c->annee_universitaire }}</td>
                    <td class="px-6 py-4 text-sm">{{ $c->date_ouverture->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 text-sm">{{ $c->date_cloture->format('d/m/Y') }}</td>
                    <td class="px-6 py-4"><span class="px-2 py-1 text-xs rounded-full {{ $c->estOuverte() ? 'bg-green-100 text-green-800' : ($c->active ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">{{ $c->estOuverte() ? 'Ouverte' : ($c->active ? 'Active' : 'Fermée') }}</span></td>
                    <td class="px-6 py-4 text-sm space-x-2"><a href="{{ route('admin.campagnes.edit', $c) }}" class="text-indigo-600">Modifier</a><form method="POST" action="{{ route('admin.campagnes.toggle', $c) }}" class="inline">@csrf<button type="submit" class="{{ $c->active ? 'text-red-600' : 'text-green-600' }}">{{ $c->active ? 'Fermer' : 'Ouvrir' }}</button></form></td>
                </tr>@empty<tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">Aucune campagne.</td></tr>@endforelse
            </tbody>
        </table><div class="mt-4">{{ $campagnes->links() }}</div></div></div>
    </div></div>
</x-app-layout>
