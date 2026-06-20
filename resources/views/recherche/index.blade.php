<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Recherche</h2></x-slot>

    <div class="py-12"><div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        <form method="GET" action="{{ route('recherche') }}" class="mb-6">
            <div class="flex gap-2">
                <input type="text" name="q" value="{{ $q }}" placeholder="Rechercher un dossier, étudiant, convention, paiement..." autofocus class="flex-1 rounded-lg border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Rechercher</button>
            </div>
        </form>

        @if($q)
            <p class="text-sm text-gray-500 mb-4">{{ count($resultats) }} résultat(s) pour « <strong>{{ $q }}</strong> »</p>
        @endif

        <div class="space-y-3">
            @forelse($resultats as $r)
            <a href="{{ $r['lien'] }}" class="block bg-white rounded-lg shadow-sm p-4 hover:shadow-md transition border-l-4 border-{{ $r['color'] }}-500">
                <div class="flex justify-between items-center">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="px-2 py-0.5 text-xs rounded bg-gray-100 text-gray-600 font-medium">{{ $r['type'] }}</span>
                            <h4 class="font-medium text-gray-800">{{ $r['titre'] }}</h4>
                        </div>
                        <p class="text-sm text-gray-500">{{ $r['description'] }}</p>
                    </div>
                    <span class="px-2 py-1 text-xs rounded-full bg-{{ $r['color'] }}-100 text-{{ $r['color'] }}-800">{{ $r['statut'] }}</span>
                </div>
            </a>
            @empty
                @if($q)
                    <div class="text-center py-12 text-gray-500">Aucun résultat trouvé.</div>
                @else
                    <div class="text-center py-12 text-gray-500">Tapez au moins 2 caractères pour rechercher.</div>
                @endif
            @endforelse
        </div>
    </div></div>
</x-app-layout>
