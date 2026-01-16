<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Facture {{ $invoice->number }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #0A4D68; padding-bottom: 10px; margin-bottom: 20px; }
        .logo { font-size: 24px; font-weight: bold; color: #0A4D68; }
        .invoice-info { display: table; width: 100%; margin-bottom: 20px; }
        .invoice-info div { display: table-cell; width: 50%; }
        .client-info { background: #f9f9f9; padding: 15px; border-radius: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background: #0A4D68; color: white; padding: 10px; text-align: left; }
        td { border-bottom: 1px solid #eee; padding: 10px; }
        .totals { float: right; width: 300px; }
        .totals-row { display: table; width: 100%; padding: 5px 0; }
        .totals-row div { display: table-cell; }
        .label { font-weight: bold; }
        .value { text-align: right; }
        .grand-total { font-size: 16px; font-weight: bold; border-top: 2px solid #0A4D68; padding-top: 10px; margin-top: 10px; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #999; }
    </style>
</head>
<body>
    <div class="header">
        @if($logoBase64)
            <img src="{{ $logoBase64 }}" style="height: 60px; margin-bottom: 10px;">
        @else
            <div class="logo">{{ $agency->name ?? 'AGENCE DE TRANSIT' }}</div>
        @endif
        <div>{{ $agency->address ?? 'Lomé, Togo' }} @if($agency && $agency->phone) - Tél: {{ $agency->phone }} @endif</div>
    </div>

    <div class="invoice-info">
        <div>
            <span class="label">Numéro:</span> {{ $invoice->number }}<br>
            <span class="label">Date:</span> {{ $invoice->date->format('d/m/Y') }}<br>
            <span class="label">Dossier:</span> {{ $invoice->dossier->reference }}
        </div>
        <div class="client-info">
            <span class="label">CLIENT:</span><br>
            <strong>{{ $invoice->dossier->client->name }}</strong><br>
            NIF: {{ $invoice->dossier->client->nif }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th style="text-align: right;">Montant (FCFA)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
            <tr>
                <td>{{ $item->description }} @if($item->is_debours) <small>(Débours)</small> @endif</td>
                <td style="text-align: right;">{{ number_format($item->amount, 0, ',', ' ') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div class="totals-row">
            <div class="label">Total Honoraires:</div>
            <div class="value">{{ number_format($invoice->subtotal_honoraires, 0, ',', ' ') }} FCFA</div>
        </div>
        <div class="totals-row">
            <div class="label">Total Débours:</div>
            <div class="value">{{ number_format($invoice->subtotal_debours, 0, ',', ' ') }} FCFA</div>
        </div>
        <div class="totals-row">
            <div class="label">TVA (18%):</div>
            <div class="value">{{ number_format($invoice->tax_amount, 0, ',', ' ') }} FCFA</div>
        </div>
        <div class="totals-row grand-total">
            <div class="label">TOTAL À PAYER:</div>
            <div class="value">{{ number_format($invoice->total_amount, 0, ',', ' ') }} FCFA</div>
        </div>
    </div>

    <div class="footer">
        {{ $agency->name ?? 'Agence' }} - @if($agency && $agency->capital) Capital: {{ number_format($agency->capital, 0, ',', ' ') }} FCFA @endif - NIF: {{ $agency->nif ?? '...' }} - RCCM: {{ $agency->rccm ?? '...' }}<br>
        <span style="color: #ccc; font-size: 8px;">Propulsé par Transit Plus (Nataan Group)</span>
    </div>
</body>
</html>
