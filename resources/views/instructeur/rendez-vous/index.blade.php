<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Mes rendez-vous</h2></x-slot>

    <div class="py-12"><div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if(session('success'))<div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">{{ session('success') }}</div>@endif

        <div class="space-y-4">
            @forelse($rdvs as $rdv)
            <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-{{ $rdv->statut_color }}-500">
                <div class="flex justify-between items-start">
                    <div>
                        <h4 class="font-semibold text-gray-800">{{ $rdv->etudiant->name }}</h4>
                        <p class="text-sm text-gray-500">Dossier : {{ $rdv->dossier->reference }}</p>
                        <p class="text-sm text-gray-600 mt-1"><strong>Date :</strong> {{ $rdv->date_heure->format('d/m/Y à H:i') }}</p>
                        <p class="text-sm text-gray-600"><strong>Motif :</strong> {{ $rdv->motif }}</p>
                        @if($rdv->lieu)<p class="text-sm text-gray-600"><strong>Lieu :</strong> {{ $rdv->lieu }}</p>@endif
                    </div>
                    <div class="text-right">
                        <span class="px-2 py-1 text-xs rounded-full bg-{{ $rdv->statut_color }}-100 text-{{ $rdv->statut_color }}-800">{{ $rdv->statut_label }}</span>

                        @if($rdv->statut === 'demande')
                        <div class="mt-3 space-y-2">
                            <form method="POST" action="{{ route('instructeur.rendez-vous.confirmer', $rdv) }}">@csrf
                                <button class="w-full px-3 py-1 bg-green-600 text-white text-xs rounded-md hover:bg-green-700">Confirmer</button>
                            </form>
                            <button onclick="document.getElementById('refuser-{{ $rdv->id }}').classList.toggle('hidden')" class="w-full px-3 py-1 bg-red-600 text-white text-xs rounded-md hover:bg-red-700">Refuser</button>
                        </div>
                        @endif

                        @if($rdv->statut === 'confirme')
                        <form method="POST" action="{{ route('instructeur.rendez-vous.terminer', $rdv) }}" class="mt-3">@csrf
                            <input type="hidden" name="note_instructeur" value="">
                            <button class="px-3 py-1 bg-blue-600 text-white text-xs rounded-md hover:bg-blue-700">Marquer terminé</button>
                        </form>
                        @endif
                    </div>
                </div>

                @if($rdv->statut === 'demande')
                <div id="refuser-{{ $rdv->id }}" class="hidden mt-3 p-3 bg-red-50 rounded">
                    <form method="POST" action="{{ route('instructeur.rendez-vous.refuser', $rdv) }}">@csrf
                        <textarea name="commentaire_refus" rows="2" required placeholder="Motif du refus..." class="w-full rounded-md border-gray-300 text-sm mb-2"></textarea>
                        <button type="submit" class="px-3 py-1 bg-red-600 text-white text-xs rounded-md">Confirmer le refus</button>
                    </form>
                </div>
                @endif

                @if($rdv->note_instructeur)
                <div class="mt-3 p-3 bg-blue-50 rounded">
                    <p class="text-sm text-blue-800"><strong>Note :</strong> {{ $rdv->note_instructeur }}</p>
                </div>
                @endif
            </div>
            @empty
            <div class="text-center py-12 text-gray-500">Aucun rendez-vous.</div>
            @endforelse
        </div>
        <div class="mt-4">{{ $rdvs->links() }}</div>
    </div></div>
</x-app-layout>
