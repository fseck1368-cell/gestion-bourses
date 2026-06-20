<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Nouvelle campagne</h2></x-slot>
    <div class="py-12"><div class="max-w-2xl mx-auto sm:px-6 lg:px-8"><div class="bg-white shadow-sm sm:rounded-lg"><div class="p-6">
        <form method="POST" action="{{ route('admin.campagnes.store') }}">@csrf
            <div class="mb-4"><label class="block text-sm font-medium text-gray-700">Nom *</label><input type="text" name="nom" value="{{ old('nom') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>@error('nom')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
            <div class="mb-4"><label class="block text-sm font-medium text-gray-700">Description</label><textarea name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea></div>
            <div class="mb-4"><label class="block text-sm font-medium text-gray-700">Année universitaire *</label><input type="text" name="annee_universitaire" value="{{ old('annee_universitaire', date('Y').'-'.(date('Y')+1)) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required></div>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div><label class="block text-sm font-medium text-gray-700">Date d'ouverture *</label><input type="date" name="date_ouverture" value="{{ old('date_ouverture') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required></div>
                <div><label class="block text-sm font-medium text-gray-700">Date de clôture *</label><input type="date" name="date_cloture" value="{{ old('date_cloture') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required></div>
            </div>
            <div class="flex justify-end gap-4"><a href="{{ route('admin.campagnes.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md">Annuler</a><button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Créer</button></div>
        </form>
    </div></div></div></div>
</x-app-layout>
