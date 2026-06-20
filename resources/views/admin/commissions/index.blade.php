<x-app-layout>
    <x-slot name="header"><div class="flex justify-between items-center"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Commissions</h2><a href="{{ route('admin.commissions.create') }}" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">+ Nouvelle commission</a></div></x-slot>
    <div class="py-12"><div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if(session('success'))<div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">{{ session('success') }}</div>@endif
        <div class="bg-white shadow-sm sm:rounded-lg"><div class="p-6"><table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50"><tr><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Membres</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dossiers</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th></tr></thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($commissions as $c)<tr>
                    <td class="px-6 py-4 text-sm font-medium">{{ $c->nom }}</td>
                    <td class="px-6 py-4 text-sm">{{ $c->date_deliberation->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 text-sm">{{ $c->membres_count }}</td>
                    <td class="px-6 py-4 text-sm">{{ $c->dossiers_count }}</td>
                    <td class="px-6 py-4"><span class="px-2 py-1 text-xs rounded-full {{ $c->statut === 'terminee' ? 'bg-green-100 text-green-800' : ($c->statut === 'en_cours' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">{{ ucfirst(str_replace('_', ' ', $c->statut)) }}</span></td>
                    <td class="px-6 py-4 text-sm"><a href="{{ route('admin.commissions.show', $c) }}" class="text-indigo-600 hover:text-indigo-900">Voir</a></td>
                </tr>@empty<tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">Aucune commission.</td></tr>@endforelse
            </tbody>
        </table><div class="mt-4">{{ $commissions->links() }}</div></div></div>
    </div></div>
</x-app-layout>
