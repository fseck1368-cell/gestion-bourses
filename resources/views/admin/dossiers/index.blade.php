<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Dossiers') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ session('success') }}
                </div>
            @endif

            <!-- Filtres -->
            <div class="bg-white shadow-sm sm:rounded-lg mb-6 p-4">
                <form method="GET" class="flex flex-wrap gap-4 items-end">
                    <div>
                        <label class="block text-sm text-gray-600">{{ __('Search') }}</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Référence, nom, n° étudiant..." class="mt-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600">{{ __('Status') }}</label>
                        <select name="statut" class="mt-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Tous</option>
                            <option value="soumis" {{ request('statut') == 'soumis' ? 'selected' : '' }}>{{ __('Submitted') }}</option>
                            <option value="en_cours_instruction" {{ request('statut') == 'en_cours_instruction' ? 'selected' : '' }}>{{ __('In progress') }}</option>
                            <option value="accepte" {{ request('statut') == 'accepte' ? 'selected' : '' }}>{{ __('Accepted') }}</option>
                            <option value="rejete" {{ request('statut') == 'rejete' ? 'selected' : '' }}>{{ __('Rejected') }}</option>
                        </select>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">{{ __('Filter') }}</button>
                    <a href="{{ route('admin.dossiers.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">Réinitialiser</a>
                </form>
            </div>

            <!-- Assignation en masse -->
            <div x-data="{ showBulk: false, selected: [] }" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 bg-gray-50 border-b flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <button @click="showBulk = !showBulk" class="px-3 py-1.5 text-sm bg-indigo-100 text-indigo-700 rounded-md hover:bg-indigo-200 font-medium">
                            {{ __('Assign') }} en masse
                        </button>
                        <span x-show="selected.length > 0" x-text="selected.length + ' sélectionné(s)'" class="text-sm text-indigo-600 font-medium"></span>
                    </div>
                </div>

                <!-- Formulaire d'assignation en masse -->
                <div x-show="showBulk" x-transition class="p-4 bg-indigo-50 border-b">
                    <form method="POST" action="{{ route('admin.dossiers.assigner.masse') }}" class="flex items-end gap-4">
                        @csrf
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Instructeur</label>
                            <select name="instructeur_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">-- Choisir un instructeur --</option>
                                @foreach(\App\Models\User::where('role', 'instructeur')->where('actif', true)->get() as $inst)
                                    <option value="{{ $inst->id }}">{{ $inst->prenom }} {{ $inst->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <template x-for="id in selected" :key="id">
                            <input type="hidden" name="dossier_ids[]" :value="id">
                        </template>
                        <button type="submit" x-bind:disabled="selected.length === 0" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed font-medium">
                            {{ __('Assign') }} (<span x-text="selected.length"></span>)
                        </button>
                    </form>
                </div>

                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left" x-show="showBulk">
                                        <input type="checkbox" @change="selected = $event.target.checked ? [...document.querySelectorAll('.dossier-check')].map(el => el.value) : []" class="rounded border-gray-300 text-indigo-600">
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Reference') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Name') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Field of study') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Instructeur</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Status') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Date') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($dossiers as $dossier)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-4" x-show="showBulk">
                                            @if($dossier->statut === 'soumis')
                                            <input type="checkbox" value="{{ $dossier->id }}" class="dossier-check rounded border-gray-300 text-indigo-600" @change="$event.target.checked ? selected.push('{{ $dossier->id }}') : selected = selected.filter(id => id !== '{{ $dossier->id }}')">
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm font-mono font-medium text-indigo-700">{{ $dossier->reference }}</td>
                                        <td class="px-6 py-4 text-sm">{{ $dossier->etudiant->prenom }} {{ $dossier->etudiant->nom }}</td>
                                        <td class="px-6 py-4 text-sm">{{ $dossier->filiere }}</td>
                                        <td class="px-6 py-4 text-sm">{{ $dossier->instructeur ? $dossier->instructeur->prenom . ' ' . $dossier->instructeur->nom : '-' }}</td>
                                        <td class="px-6 py-4">
                                            @php $statusColors = ['soumis' => 'yellow', 'en_cours_instruction' => 'blue', 'accepte' => 'green', 'rejete' => 'red']; $c = $statusColors[$dossier->statut] ?? 'gray'; @endphp
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $c }}-100 text-{{ $c }}-800">
                                                {{ $dossier->statut_label }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">{{ $dossier->created_at->format('d/m/Y') }}</td>
                                        <td class="px-6 py-4 text-sm">
                                            <a href="{{ route('admin.dossiers.show', $dossier) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">{{ __('View') }}</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="8" class="px-6 py-8 text-center text-gray-500">{{ __('No data found') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $dossiers->withQueryString()->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
