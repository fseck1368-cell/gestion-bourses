<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Statistiques</h2></x-slot>
    <div class="py-12"><div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg shadow text-center">
                <div class="text-3xl font-bold text-green-600">{{ $tauxAcceptation }}%</div>
                <div class="text-gray-500">Taux d'acceptation</div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow text-center">
                <div class="text-3xl font-bold text-blue-600">{{ round($moyenneTraitement ?? 0) }} jours</div>
                <div class="text-gray-500">Délai moyen de traitement</div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow text-center">
                <div class="text-3xl font-bold text-indigo-600">{{ array_sum($parStatut) }}</div>
                <div class="text-gray-500">Total dossiers</div>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white p-6 rounded-lg shadow"><h3 class="font-medium mb-4">Dossiers par mois ({{ date('Y') }})</h3><canvas id="chartMois"></canvas></div>
            <div class="bg-white p-6 rounded-lg shadow"><h3 class="font-medium mb-4">Répartition par statut</h3><canvas id="chartStatut"></canvas></div>
            <div class="bg-white p-6 rounded-lg shadow"><h3 class="font-medium mb-4">Top filières</h3><canvas id="chartFiliere"></canvas></div>
            <div class="bg-white p-6 rounded-lg shadow"><h3 class="font-medium mb-4">Par niveau d'étude</h3><canvas id="chartNiveau"></canvas></div>
        </div>
        <div class="mt-6 flex gap-4">
            <a href="{{ route('admin.export.rapport') }}" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">Exporter PDF</a>
            <a href="{{ route('admin.export.csv') }}" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Exporter CSV</a>
        </div>
    </div></div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    const moisLabels = ['Jan','Fév','Mar','Avr','Mai','Jun','Jul','Aoû','Sep','Oct','Nov','Déc'];
    @php $moisComplet = array_values(array_replace(array_fill(1,12,0), $dossiersParMois)); @endphp
    const moisData = @json($moisComplet);
    new Chart(document.getElementById('chartMois'), {type:'bar', data:{labels:moisLabels, datasets:[{label:'Dossiers', data:moisData, backgroundColor:'rgba(99,102,241,0.5)'}]}});
    new Chart(document.getElementById('chartStatut'), {type:'doughnut', data:{labels:@json(array_keys($parStatut)), datasets:[{data:@json(array_values($parStatut)), backgroundColor:['#EAB308','#3B82F6','#22C55E','#EF4444']}]}});
    new Chart(document.getElementById('chartFiliere'), {type:'bar', data:{labels:@json(array_keys($parFiliere)), datasets:[{label:'Dossiers', data:@json(array_values($parFiliere)), backgroundColor:'rgba(16,185,129,0.5)'}]}, options:{indexAxis:'y'}});
    new Chart(document.getElementById('chartNiveau'), {type:'pie', data:{labels:@json(array_keys($parNiveau)), datasets:[{data:@json(array_values($parNiveau)), backgroundColor:['#6366F1','#8B5CF6','#EC4899','#F59E0B','#10B981','#06B6D4']}]}});
    </script>
</x-app-layout>
