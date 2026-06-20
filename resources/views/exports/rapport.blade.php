<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>Rapport Bourses</title>
<style>body{font-family:sans-serif;font-size:11px;margin:30px}h1{text-align:center;font-size:16px}table{width:100%;border-collapse:collapse;margin:15px 0;font-size:10px}th,td{padding:4px 6px;border:1px solid #ddd;text-align:left}th{background:#4F46E5;color:white}</style>
</head><body>
<h1>RAPPORT DES DEMANDES DE BOURSES</h1>
<p style="text-align:center">Généré le {{ now()->format('d/m/Y à H:i') }}</p>
<table><tr><th>Statut</th><th>Nombre</th><th>%</th></tr>
<tr><td>Total</td><td>{{ $stats['total'] }}</td><td>100%</td></tr>
<tr><td>Acceptés</td><td>{{ $stats['acceptes'] }}</td><td>{{ $stats['total'] > 0 ? round($stats['acceptes']/$stats['total']*100,1) : 0 }}%</td></tr>
<tr><td>Rejetés</td><td>{{ $stats['rejetes'] }}</td><td>{{ $stats['total'] > 0 ? round($stats['rejetes']/$stats['total']*100,1) : 0 }}%</td></tr>
<tr><td>En instruction</td><td>{{ $stats['en_cours'] }}</td><td>{{ $stats['total'] > 0 ? round($stats['en_cours']/$stats['total']*100,1) : 0 }}%</td></tr>
<tr><td>Soumis</td><td>{{ $stats['soumis'] }}</td><td>{{ $stats['total'] > 0 ? round($stats['soumis']/$stats['total']*100,1) : 0 }}%</td></tr>
</table>
<h2>Liste des dossiers ({{ $dossiers->count() }})</h2>
<table><thead><tr><th>Réf</th><th>Étudiant</th><th>Filière</th><th>Niveau</th><th>Statut</th><th>Date</th></tr></thead><tbody>
@foreach($dossiers as $d)<tr><td>{{ $d->reference }}</td><td>{{ $d->etudiant->prenom }} {{ $d->etudiant->nom }}</td><td>{{ $d->filiere }}</td><td>{{ $d->niveau_etude }}</td><td>{{ $d->statut }}</td><td>{{ $d->created_at->format('d/m/Y') }}</td></tr>@endforeach
</tbody></table>
</body></html>
