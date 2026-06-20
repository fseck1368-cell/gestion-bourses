<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Paramètres du système</h2></x-slot>

    <div class="py-12"><div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        @if(session('success'))<div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">{{ session('success') }}</div>@endif

        <form method="POST" action="{{ route('admin.parametres.update') }}">
            @csrf @method('PUT')

            @foreach($parametres as $groupe => $params)
            <div class="bg-white shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="font-semibold text-lg mb-4 capitalize border-b pb-2">
                    @php
                        $labels = ['general' => 'Général', 'financier' => 'Financier', 'delais' => 'Délais', 'criteres' => 'Critères', 'emails' => 'Emails', 'alertes' => 'Alertes'];
                    @endphp
                    {{ $labels[$groupe] ?? ucfirst($groupe) }}
                </h3>
                <div class="space-y-4">
                    @foreach($params as $param)
                    <div class="grid grid-cols-3 gap-4 items-center">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ $param->label }}</label>
                            @if($param->description)
                                <p class="text-xs text-gray-400">{{ $param->description }}</p>
                            @endif
                        </div>
                        <div class="col-span-2">
                            @if($param->type === 'number')
                                <input type="number" name="parametres[{{ $param->cle }}]" value="{{ $param->valeur }}" class="w-full rounded-md border-gray-300 text-sm">
                            @elseif($param->type === 'email')
                                <input type="email" name="parametres[{{ $param->cle }}]" value="{{ $param->valeur }}" class="w-full rounded-md border-gray-300 text-sm">
                            @else
                                <input type="text" name="parametres[{{ $param->cle }}]" value="{{ $param->valeur }}" class="w-full rounded-md border-gray-300 text-sm">
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach

            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Enregistrer les paramètres</button>
            </div>
        </form>
    </div></div>
</x-app-layout>
