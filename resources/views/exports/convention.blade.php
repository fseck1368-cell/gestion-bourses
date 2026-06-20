<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Convention {{ $convention->reference }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; line-height: 1.6; margin: 40px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 15px; }
        .header h1 { font-size: 18px; margin: 0; }
        .header p { color: #666; margin: 5px 0 0; }
        .section { margin-bottom: 20px; }
        .section h3 { font-size: 14px; border-bottom: 1px solid #ddd; padding-bottom: 5px; color: #333; }
        .info-grid { display: table; width: 100%; }
        .info-row { display: table-row; }
        .info-label { display: table-cell; width: 40%; padding: 4px 0; font-weight: bold; color: #555; }
        .info-value { display: table-cell; padding: 4px 0; }
        .signature { margin-top: 50px; display: table; width: 100%; }
        .sig-block { display: table-cell; width: 50%; text-align: center; }
        .sig-line { border-top: 1px solid #333; width: 150px; margin: 40px auto 5px; }
        .footer { margin-top: 40px; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #eee; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>CONVENTION D'ATTRIBUTION DE BOURSE</h1>
        <p>Référence : {{ $convention->reference }}</p>
    </div>

    <div class="section">
        <h3>Parties</h3>
        <p><strong>L'établissement</strong>, représenté par son administration,</p>
        <p><strong>L'étudiant(e)</strong> : {{ $convention->etudiant->prenom }} {{ $convention->etudiant->nom }}
            @if($convention->etudiant->numero_etudiant) (N° {{ $convention->etudiant->numero_etudiant }})@endif</p>
    </div>

    <div class="section">
        <h3>Objet et durée</h3>
        <div class="info-grid">
            <div class="info-row"><span class="info-label">Dossier de référence</span><span class="info-value">{{ $convention->dossier->reference }}</span></div>
            <div class="info-row"><span class="info-label">Date de début</span><span class="info-value">{{ $convention->date_debut->format('d/m/Y') }}</span></div>
            <div class="info-row"><span class="info-label">Date de fin</span><span class="info-value">{{ $convention->date_fin->format('d/m/Y') }}</span></div>
            <div class="info-row"><span class="info-label">Durée</span><span class="info-value">{{ $convention->duree_mois }} mois</span></div>
            <div class="info-row"><span class="info-label">Montant mensuel</span><span class="info-value">{{ number_format($convention->montant_mensuel, 2, ',', ' ') }} DH</span></div>
            <div class="info-row"><span class="info-label">Montant total</span><span class="info-value">{{ number_format($convention->montant_total, 2, ',', ' ') }} DH</span></div>
        </div>
    </div>

    @if($convention->conditions)
    <div class="section">
        <h3>Conditions</h3>
        <p>{{ $convention->conditions }}</p>
    </div>
    @endif

    @if($convention->obligations_etudiant)
    <div class="section">
        <h3>Obligations de l'étudiant(e)</h3>
        <p>{{ $convention->obligations_etudiant }}</p>
    </div>
    @endif

    <div class="signature">
        <div class="sig-block">
            <div class="sig-line"></div>
            <p>L'Administration</p>
            <p style="font-size:10px">Date : {{ $convention->date_signature?->format('d/m/Y') ?? '___/___/______' }}</p>
        </div>
        <div class="sig-block">
            <div class="sig-line"></div>
            <p>L'Étudiant(e)</p>
            <p style="font-size:10px">Date : ___/___/______</p>
        </div>
    </div>

    <div class="footer">
        Document généré le {{ now()->format('d/m/Y à H:i') }}
    </div>
</body>
</html>
