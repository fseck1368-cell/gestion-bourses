<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Alertes & Rappels</h2>
            <form method="POST" action="{{ route('admin.alertes.lire-tout') }}">@csrf
                <button class="px-4 py-2 bg-gray-600 text-white text-sm rounded-md hover:bg-gray-700">Tout marquer comme lu</button>
            </form>
        </div>
    </x-slot>

    <div class="py-12"><div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if(session('success'))<div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">{{ session('success') }}</div>@endif

        <div class="space-y-3">
            @forelse($alertes as $alerte)
            <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-{{ $alerte->niveau_color }}-500 {{ $alerte->lue ? 'opacity-60' : '' }}">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="px-2 py-0.5 text-xs rounded-full bg-{{ $alerte->niveau_color }}-100 text-{{ $alerte->niveau_color }}-800 font-medium">
                                {{ ucfirst($alerte->niveau) }}
                            </span>
                            <h4 class="font-semibold text-gray-800">{{ $alerte->titre }}</h4>
                        </div>
                        <p class="text-sm text-gray-600">{{ $alerte->message }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $alerte->created_at->diffForHumans() }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($alerte->lien)
                            <a href="{{ $alerte->lien }}" class="text-indigo-600 text-sm hover:underline">Voir</a>
                        @endif
                        @if(!$alerte->lue)
                            <form method="POST" action="{{ route('admin.alertes.lue', $alerte) }}">@csrf
                                <button class="text-gray-400 hover:text-gray-600 text-xs">Marquer lu</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-12 text-gray-500">Aucune alerte active.</div>
            @endforelse
        </div>
        <div class="mt-4">{{ $alertes->links() }}</div>
    </div></div>
</x-app-layout>
