<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Commission : {{ $commission->nom }}</h2></x-slot>
    <div class="py-12"><div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        @if(session('success'))<div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">{{ session('success') }}</div>@endif

        <div class="bg-white shadow-sm sm:rounded-lg mb-6"><div class="p-6">
            <dl class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div><dt class="text-sm text-gray-500">Date</dt><dd class="font-medium">{{ $commission->date_deliberation->format('d/m/Y') }}</dd></div>
                <div><dt class="text-sm text-gray-500">Statut</dt><dd><span class="px-2 py-1 text-xs rounded-full {{ $commission->statut === 'terminee' ? 'bg-green-100 text-green-800' : ($commission->statut === 'en_cours' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">{{ ucfirst(str_replace('_',' ',$commission->statut)) }}</span></dd></div>
                <div><dt class="text-sm text-gray-500">Membres</dt><dd class="font-medium">{{ $commission->membres->count() }}</dd></div>
                <div><dt class="text-sm text-gray-500">Dossiers</dt><dd class="font-medium">{{ $commission->dossiers->count() }}</dd></div>
            </dl>
            <div class="mt-4 flex gap-3">
                @if($commission->statut === 'planifiee')<form method="POST" action="{{ route('admin.commissions.demarrer', $commission) }}">@csrf<button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Démarrer la délibération</button></form>@endif
                @if($commission->statut === 'en_cours')<form method="POST" action="{{ route('admin.commissions.terminer', $commission) }}">@csrf<button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700" onclick="return confirm('Terminer et appliquer les décisions ?')">Terminer et appliquer</button></form>@endif
            </div>
        </div></div>

        <div class="bg-white shadow-sm sm:rounded-lg mb-6"><div class="p-6"><h3 class="font-medium mb-4">Membres</h3><div class="flex flex-wrap gap-2">@foreach($commission->membres as $m)<span class="px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-sm">{{ $m->prenom }} {{ $m->nom }}</span>@endforeach</div></div></div>

        <div class="bg-white shadow-sm sm:rounded-lg"><div class="p-6"><h3 class="font-medium mb-4">Dossiers & Votes</h3>
            @foreach($commission->dossiers as $dossier)
            <div class="border rounded-lg p-4 mb-4">
                <div class="flex justify-between items-start">
                    <div><span class="font-medium">{{ $dossier->reference }}</span> — {{ $dossier->etudiant->prenom }} {{ $dossier->etudiant->nom }}<br><span class="text-sm text-gray-500">{{ $dossier->filiere }} | {{ $dossier->niveau_etude }}</span></div>
                    @if($dossier->pivot->decision_finale)<span class="px-2 py-1 text-xs rounded-full {{ $dossier->pivot->decision_finale === 'accepte' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ ucfirst($dossier->pivot->decision_finale) }}</span>@endif
                </div>
                @php $votesD = $commission->votes->where('dossier_id', $dossier->id); @endphp
                @if($votesD->isNotEmpty())<div class="mt-2 text-sm"><span class="text-green-600 font-medium">Pour: {{ $votesD->where('vote','pour')->count() }}</span> | <span class="text-red-600 font-medium">Contre: {{ $votesD->where('vote','contre')->count() }}</span> | <span class="text-gray-500">Abstention: {{ $votesD->where('vote','abstention')->count() }}</span></div>@endif
                @if($commission->statut === 'en_cours' && $commission->membres->contains('id', auth()->id()))
                <form method="POST" action="{{ route('admin.commissions.voter', $commission) }}" class="mt-3 flex gap-2 items-end">@csrf
                    <input type="hidden" name="dossier_id" value="{{ $dossier->id }}">
                    <select name="vote" class="text-sm rounded-md border-gray-300" required><option value="pour">Pour</option><option value="contre">Contre</option><option value="abstention">Abstention</option></select>
                    <input type="text" name="commentaire" placeholder="Commentaire (optionnel)" class="text-sm rounded-md border-gray-300 flex-1">
                    <button type="submit" class="px-3 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">Voter</button>
                </form>@endif
            </div>
            @endforeach
        </div></div>
    </div></div>
</x-app-layout>
