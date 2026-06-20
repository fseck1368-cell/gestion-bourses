<x-app-layout>
    <x-slot name="header"><div class="flex justify-between items-center"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Notifications</h2><form method="POST" action="{{ route('notifications.lire-tout') }}">@csrf<button type="submit" class="text-sm text-indigo-600 hover:text-indigo-800">Tout marquer comme lu</button></form></div></x-slot>
    <div class="py-12"><div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        @if(session('success'))<div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">{{ session('success') }}</div>@endif
        <div class="bg-white shadow-sm sm:rounded-lg"><div class="p-6 divide-y divide-gray-200">
            @forelse($notifications as $n)
                <div class="py-3 {{ $n->read_at ? '' : 'bg-indigo-50 -mx-6 px-6' }}">
                    <div class="flex justify-between"><span class="text-sm font-medium">{{ $n->data['message'] ?? 'Notification' }}</span><span class="text-xs text-gray-400">{{ $n->created_at->diffForHumans() }}</span></div>
                    @if(isset($n->data['reference']))<p class="text-xs text-gray-500 mt-1">Dossier : {{ $n->data['reference'] }}</p>@endif
                </div>
            @empty<p class="text-gray-500 py-4">Aucune notification.</p>@endforelse
        </div><div class="p-4">{{ $notifications->links() }}</div></div>
    </div></div>
</x-app-layout>
