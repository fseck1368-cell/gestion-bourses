<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Mes approbations en attente</h2></x-slot>

    <div class="py-12"><div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if(session('success'))<div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">{{ session('success') }}</div>@endif

        <div class="space-y-4">
            @forelse($approbations as $app)
            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-yellow-500">
                <div class="flex justify-between items-start">
                    <div>
                        <h4 class="font-semibold text-gray-800">Dossier {{ $app->dossier->reference }}</h4>
                        <p class="text-sm text-gray-500">Étudiant : {{ $app->dossier->etudiant->name }}</p>
                        <p class="text-sm text-gray-500">Niveau d'approbation : {{ $app->ordre }}</p>
                        <p class="text-xs text-gray-400 mt-1">Soumis {{ $app->created_at->diffForHumans() }}</p>
                    </div>
                    <div class="flex gap-2">
                        <form method="POST" action="{{ route('admin.approbations.approuver', $app) }}">
                            @csrf
                            <input type="hidden" name="commentaire" value="">
                            <button class="px-4 py-2 bg-green-600 text-white text-sm rounded-md hover:bg-green-700">Approuver</button>
                        </form>
                        <button onclick="document.getElementById('rejeter-{{ $app->id }}').classList.toggle('hidden')" class="px-4 py-2 bg-red-600 text-white text-sm rounded-md hover:bg-red-700">Rejeter</button>
                    </div>
                </div>
                <div id="rejeter-{{ $app->id }}" class="hidden mt-4 p-4 bg-red-50 rounded-lg">
                    <form method="POST" action="{{ route('admin.approbations.rejeter', $app) }}">
                        @csrf
                        <textarea name="commentaire" rows="2" required placeholder="Motif du rejet..." class="w-full rounded-md border-gray-300 text-sm mb-2"></textarea>
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white text-sm rounded-md">Confirmer le rejet</button>
                    </form>
                </div>
            </div>
            @empty
            <div class="text-center py-12 text-gray-500">Aucune approbation en attente.</div>
            @endforelse
        </div>
        <div class="mt-4">{{ $approbations->links() }}</div>
    </div></div>
</x-app-layout>
