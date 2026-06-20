<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Paiement {{ $paiement->reference }}</h2></x-slot>

    <div class="py-12"><div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        @if(session('success'))<div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">{{ session('success') }}</div>@endif

        <div class="bg-white shadow-sm sm:rounded-lg p-6">
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <h3 class="font-semibold text-lg mb-4">Informations</h3>
                    <dl class="space-y-2">
                        <div><dt class="text-sm text-gray-500">Référence</dt><dd class="font-mono">{{ $paiement->reference }}</dd></div>
                        <div><dt class="text-sm text-gray-500">Étudiant</dt><dd>{{ $paiement->etudiant->name }}</dd></div>
                        <div><dt class="text-sm text-gray-500">Dossier</dt><dd>{{ $paiement->dossier->reference }}</dd></div>
                        <div><dt class="text-sm text-gray-500">Montant</dt><dd class="text-lg font-bold">{{ number_format($paiement->montant, 2, ',', ' ') }} DH</dd></div>
                        <div><dt class="text-sm text-gray-500">Statut</dt><dd><span class="px-2 py-1 text-xs rounded-full bg-{{ $paiement->statut_color }}-100 text-{{ $paiement->statut_color }}-800">{{ $paiement->statut_label }}</span></dd></div>
                    </dl>
                </div>
                <div>
                    <h3 class="font-semibold text-lg mb-4">Détails bancaires</h3>
                    <dl class="space-y-2">
                        <div><dt class="text-sm text-gray-500">Mode</dt><dd class="capitalize">{{ $paiement->mode_paiement }}</dd></div>
                        <div><dt class="text-sm text-gray-500">Banque</dt><dd>{{ $paiement->banque ?? '-' }}</dd></div>
                        <div><dt class="text-sm text-gray-500">N° Compte</dt><dd>{{ $paiement->numero_compte ?? '-' }}</dd></div>
                        <div><dt class="text-sm text-gray-500">Réf. bancaire</dt><dd>{{ $paiement->reference_bancaire ?? '-' }}</dd></div>
                        <div><dt class="text-sm text-gray-500">Date prévue</dt><dd>{{ $paiement->date_prevue?->format('d/m/Y') ?? '-' }}</dd></div>
                        <div><dt class="text-sm text-gray-500">Date versement</dt><dd>{{ $paiement->date_versement?->format('d/m/Y') ?? '-' }}</dd></div>
                    </dl>
                </div>
            </div>

            @if($paiement->commentaire)
                <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-500 mb-1">Commentaire</p>
                    <p>{{ $paiement->commentaire }}</p>
                </div>
            @endif

            <div class="mt-6 flex gap-2">
                @if($paiement->statut === 'en_attente')
                    <form method="POST" action="{{ route('admin.paiements.valider', $paiement) }}">@csrf<button class="px-4 py-2 bg-blue-600 text-white rounded-md">Valider</button></form>
                    <form method="POST" action="{{ route('admin.paiements.annuler', $paiement) }}">@csrf<button class="px-4 py-2 bg-red-600 text-white rounded-md">Annuler</button></form>
                @endif
                @if($paiement->statut === 'valide')
                    <form method="POST" action="{{ route('admin.paiements.verser', $paiement) }}">@csrf<button class="px-4 py-2 bg-green-600 text-white rounded-md">Marquer comme versé</button></form>
                @endif
            </div>
        </div>
    </div></div>
</x-app-layout>
