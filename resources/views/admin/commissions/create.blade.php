<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Créer une commission</h2></x-slot>
    <div class="py-12"><div class="max-w-3xl mx-auto sm:px-6 lg:px-8"><div class="bg-white shadow-sm sm:rounded-lg"><div class="p-6">
        <form method="POST" action="{{ route('admin.commissions.store') }}">@csrf
            <div class="mb-4"><label class="block text-sm font-medium text-gray-700">Nom *</label><input type="text" name="nom" value="{{ old('nom') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required></div>
            <div class="mb-4"><label class="block text-sm font-medium text-gray-700">Date de délibération *</label><input type="date" name="date_deliberation" value="{{ old('date_deliberation') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required></div>
            <div class="mb-4"><label class="block text-sm font-medium text-gray-700">Membres (instructeurs) *</label><select name="membres[]" multiple class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 h-32" required>@foreach($instructeurs as $i)<option value="{{ $i->id }}">{{ $i->prenom }} {{ $i->nom }}</option>@endforeach</select><p class="text-xs text-gray-500 mt-1">Maintenez Ctrl pour sélectionner plusieurs</p></div>
            <div class="mb-4"><label class="block text-sm font-medium text-gray-700">Dossiers à examiner *</label><select name="dossiers[]" multiple class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 h-32" required>@foreach($dossiers as $d)<option value="{{ $d->id }}">{{ $d->reference }} - {{ $d->etudiant->prenom }} {{ $d->etudiant->nom }} ({{ $d->filiere }})</option>@endforeach</select></div>
            <div class="flex justify-end gap-4"><a href="{{ route('admin.commissions.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md">Annuler</a><button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Créer</button></div>
        </form>
    </div></div></div></div>
</x-app-layout>
