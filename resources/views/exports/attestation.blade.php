<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Attestation de bourse</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; line-height: 1.8; margin: 50px; }
        .header { text-align: center; margin-bottom: 40px; }
        .header h1 { font-size: 20px; text-decoration: underline; margin-bottom: 5px; }
        .content { margin: 30px 0; text-align: justify; }
        .footer { margin-top: 60px; }
        .signature { text-align: right; margin-top: 50px; }
        .sig-line { border-top: 1px solid #333; width: 200px; margin-left: auto; margin-top: 40px; }
    </style>
</head>
<body>
    <div class="header">
        <p style="font-size: 14px; font-weight: bold;">ÉTABLISSEMENT UNIVERSITAIRE</p>
        <p>Service des Bourses</p>
        <br>
        <h1>ATTESTATION D'ATTRIBUTION DE BOURSE</h1>
        <p>Année universitaire : {{ $dossier->annee_universitaire }}</p>
    </div>

    <div class="content">
        <p>Le service des bourses atteste que :</p>
        <br>
        <p><strong>{{ $dossier->etudiant->prenom }} {{ $dossier->etudiant->nom }}</strong></p>
        @if($dossier->etudiant->numero_etudiant)
        <p>N° étudiant : {{ $dossier->etudiant->numero_etudiant }}</p>
        @endif
        <p>Filière : {{ $dossier->filiere }}</p>
        <p>Niveau : {{ $dossier->niveau_etude }}</p>
        <p>Établissement : {{ $dossier->etablissement }}</p>
        <br>
        <p>a bénéficié d'une <strong>bourse universitaire</strong> au titre de l'année universitaire <strong>{{ $dossier->annee_universitaire }}</strong>.</p>
        <br>
        <p>Référence du dossier : <strong>{{ $dossier->reference }}</strong></p>
        <p>Date de décision : {{ $dossier->date_decision?->format('d/m/Y') ?? '-' }}</p>
        <br>
        <p>Cette attestation est délivrée à l'intéressé(e) pour servir et valoir ce que de droit.</p>
    </div>

    <div class="signature">
        <p>Fait le {{ now()->format('d/m/Y') }}</p>
        <p><strong>Le Responsable du Service des Bourses</strong></p>
        <div class="sig-line"></div>
    </div>
</body>
</html>
