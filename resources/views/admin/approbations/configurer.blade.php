<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Configurer le workflow - {{ $dossier->reference }}</h2></x-slot>

    <div class="py-12"><div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm sm:rounded-lg p-6 mb-6">
            <h3 class="font-medium mb-4">Workflow actuel</h3>
            @if($approbations->isEmpty())
                <p class="text-gray-500 text-sm">Aucun workflow configuré.</p>
            @else
                <div class="space-y-2">
                    @foreach($approbations as $app)
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded">
                        <span class="w-8 h-8 flex items-center justify-center bg-indigo-100 text-indigo-800 rounded-full font-bold text-sm">{{ $app->ordre }}</span>
                        <span class="flex-1">{{ $app->approbateur->name }}</span>
                        <span class="px-2 py-1 text-xs rounded-full bg-{{ $app->statut_color }}-100 text-{{ $app->statut_color }}-800">{{ $app->statut_label }}</span>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="bg-white shadow-sm sm:rounded-lg p-6">
            <h3 class="font-medium mb-4">Configurer les approbateurs (par ordre)</h3>
            <form method="POST" action="{{ route('admin.approbations.store', $dossier) }}">
                @csrf
                <div id="approbateurs-list" class="space-y-3 mb-4">
                    <div class="flex gap-2 items-center">
                        <span class="text-sm font-medium text-gray-500 w-8">1.</span>
                        <select name="approbateurs[]" required class="flex-1 rounded-md border-gray-300 text-sm">
                            <option value="">-- Sélectionner --</option>
                            @foreach($admins as $admin)
                                <option value="{{ $admin->id }}">{{ $admin->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <button type="button" onclick="ajouterApprobateur()" class="text-sm text-indigo-600 hover:underline mb-4">+ Ajouter un niveau</button>
                <div class="flex justify-end gap-2">
                    <a href="{{ route('admin.dossiers.show', $dossier) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md">Retour</a>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Enregistrer</button>
                </div>
            </form>
        </div>
    </div></div>

    <script>
    let compteur = 1;
    function ajouterApprobateur() {
        compteur++;
        const div = document.createElement('div');
        div.className = 'flex gap-2 items-center';
        div.innerHTML = `<span class="text-sm font-medium text-gray-500 w-8">${compteur}.</span>
            <select name="approbateurs[]" required class="flex-1 rounded-md border-gray-300 text-sm">
                <option value="">-- Sélectionner --</option>
                @foreach($admins as $admin)<option value="{{ $admin->id }}">{{ $admin->name }}</option>@endforeach
            </select>
            <button type="button" onclick="this.parentElement.remove()" class="text-red-500 text-sm">Retirer</button>`;
        document.getElementById('approbateurs-list').appendChild(div);
    }
    </script>
</x-app-layout>
