<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Journal d'audit</h2></x-slot>

    <div class="py-12"><div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Filtres -->
        <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
            <form method="GET" class="flex flex-wrap gap-3 items-end">
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Type d'action</label>
                    <select name="log_name" class="rounded-md border-gray-300 text-sm">
                        <option value="">Tous</option>
                        @foreach($logNames as $name)
                            <option value="{{ $name }}" {{ request('log_name') == $name ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Date début</label>
                    <input type="date" name="date_debut" value="{{ request('date_debut') }}" class="rounded-md border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Date fin</label>
                    <input type="date" name="date_fin" value="{{ request('date_fin') }}" class="rounded-md border-gray-300 text-sm">
                </div>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">Filtrer</button>
                <a href="{{ route('admin.audit.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm rounded-md">Reset</a>
            </form>
        </div>

        <div class="bg-white shadow-sm sm:rounded-lg"><div class="p-6">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50"><tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Utilisateur</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sujet</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($logs as $log)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3 text-sm">{{ $log->causer?->name ?? 'Système' }}</td>
                        <td class="px-4 py-3"><span class="px-2 py-0.5 text-xs rounded bg-indigo-100 text-indigo-800">{{ $log->log_name }}</span></td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $log->description }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $log->subject_type ? class_basename($log->subject_type) . ' #' . $log->subject_id : '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">Aucune activité enregistrée.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">{{ $logs->links() }}</div>
        </div></div>
    </div></div>
</x-app-layout>
