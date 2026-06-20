<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Relevé de paiements</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; line-height: 1.5; margin: 40px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 15px; }
        .header h1 { font-size: 16px; margin: 0; }
        .info { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background: #f3f4f6; padding: 8px; text-align: left; font-size: 10px; text-transform: uppercase; border-bottom: 2px solid #ddd; }
        td { padding: 8px; border-bottom: 1px solid #eee; }
        .total { margin-top: 20px; text-align: right; font-size: 14px; font-weight: bold; }
        .footer { margin-top: 40px; text-align: center; font-size: 9px; color: #999; }
    </style>
</head>
<body>
    <div class="header">
        <h1>RELEVÉ DE PAIEMENTS DE BOURSE</h1>
    </div>

    <div class="info">
        <p><strong>Étudiant(e) :</strong> {{ $user->prenom }} {{ $user->nom }}</p>
        @if($user->numero_etudiant)<p><strong>N° étudiant :</strong> {{ $user->numero_etudiant }}</p>@endif
        <p><strong>Email :</strong> {{ $user->email }}</p>
        <p><strong>Date d'édition :</strong> {{ now()->format('d/m/Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Référence</th>
                <th>Période</th>
                <th>Date versement</th>
                <th>Mode</th>
                <th style="text-align:right">Montant</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @foreach($paiements as $p)
            <tr>
                <td>{{ $p->reference }}</td>
                <td>{{ $p->periode ?? '-' }}</td>
                <td>{{ $p->date_versement?->format('d/m/Y') }}</td>
                <td>{{ ucfirst($p->mode_paiement) }}</td>
                <td style="text-align:right">{{ number_format($p->montant, 2, ',', ' ') }} DH</td>
            </tr>
            @php $total += $p->montant; @endphp
            @endforeach
        </tbody>
    </table>

    <div class="total">
        Total perçu : {{ number_format($total, 2, ',', ' ') }} DH
    </div>

    <div class="footer">
        Document généré automatiquement le {{ now()->format('d/m/Y à H:i') }} — Ne constitue pas un document officiel
    </div>
</body>
</html>
