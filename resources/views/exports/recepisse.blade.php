<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>Récépissé {{ $dossier->reference }}</title>
<style>body{font-family:sans-serif;font-size:12px;margin:40px}h1{text-align:center;font-size:18px;border-bottom:2px solid #333;padding-bottom:10px}table{width:100%;border-collapse:collapse;margin:15px 0}td{padding:6px;border:1px solid #ddd}td:first-child{font-weight:bold;width:40%;background:#f5f5f5}.footer{text-align:center;margin-top:40px;font-size:10px;color:#666}</style>
</head><body>
<div style="text-align:center;margin-bottom:30px"><h1>RÉCÉPISSÉ DE DÉPÔT DE DOSSIER</h1><p>Demande de bourse universitaire</p></div>
<table>
<tr><td>Référence</td><td>{{ $dossier->reference }}</td></tr>
<tr><td>Date de soumission</td><td>{{ $dossier->date_soumission?->format('d/m/Y à H:i') }}</td></tr>
<tr><td>Étudiant</td><td>{{ $dossier->etudiant->prenom }} {{ $dossier->etudiant->nom }}</td></tr>
<tr><td>Email</td><td>{{ $dossier->etudiant->email }}</td></tr>
<tr><td>Filière</td><td>{{ $dossier->filiere }}</td></tr>
<tr><td>Niveau</td><td>{{ $dossier->niveau_etude }}</td></tr>
<tr><td>Établissement</td><td>{{ $dossier->etablissement }}</td></tr>
<tr><td>Année universitaire</td><td>{{ $dossier->annee_universitaire }}</td></tr>
<tr><td>Documents joints</td><td>{{ $dossier->documents->count() }} document(s)</td></tr>
</table>
@if($dossier->documents->isNotEmpty())<h3>Documents déposés :</h3><ul>@foreach($dossier->documents as $doc)<li>{{ $doc->type_label }} — {{ $doc->nom_fichier }}</li>@endforeach</ul>@endif
<div class="footer"><p>Ce document atteste le dépôt de votre dossier. Conservez-le précieusement.</p><p>Généré le {{ now()->format('d/m/Y à H:i') }}</p></div>
</body></html>
