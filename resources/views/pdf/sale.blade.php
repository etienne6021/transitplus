<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>FACTURE - {{ $sale->number }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; font-size: 13px; line-height: 1.5; }
        .header { margin-bottom: 30px; border-bottom: 2px solid #f3f4f6; padding-bottom: 20px; }
        .agency-info { float: left; width: 50%; }
        .client-info { float: right; width: 40%; text-align: right; background: #f9fafb; padding: 15px; border-radius: 5px; }
        .clear { clear: both; }
        .doc-title { text-align: center; margin: 30px 0; }
        .doc-title h1 { color: #10b981; margin: 0; text-transform: uppercase; font-size: 24px; }
        .doc-title p { margin: 5px 0; color: #6b7280; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th { background: #f3f4f6; color: #374151; text-align: left; padding: 12px; border-bottom: 1px solid #e5e7eb; }
        td { padding: 12px; border-bottom: 1px solid #f3f4f6; }
        .totals { float: right; width: 300px; margin-top: 20px; }
        .total-row { padding: 10px 0; border-bottom: 1px solid #f3f4f6; }
        .total-row.grand-total { border-top: 2px solid #10b981; font-weight: bold; font-size: 16px; color: #10b981; border-bottom: none; }
        .label { color: #6b7280; }
        .value { float: right; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="agency-info">
            @if($logoBase64)
                <img src="{{ $logoBase64 }}" style="max-height: 80px; margin-bottom: 10px;">
            @endif
            <h2 style="margin:0; color:#1f2937;">{{ $agency->name }}</h2>
            <p style="margin:5px 0;">
                {{ $agency->address }}<br>
                Tél: {{ $agency->contact_phone }}<br>
                NIF: {{ $agency->nif }} | RCCM: {{ $agency->rccm }}
            </p>
        </div>
        <div class="client-info">
            <span class="label">CLIENT</span><br>
            <strong style="font-size: 15px;">{{ $sale->client->name }}</strong><br>
            {{ $sale->client->phone }}<br>
            {{ $sale->client->address }}
        </div>
        <div class="clear"></div>
    </div>

    <div class="doc-title">
        <h1>FACTURE DE VENTE</h1>
        <p>N° <strong>{{ $sale->number }}</strong> | Date: {{ $sale->date->format('d/m/Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="40%">Désignation</th>
                <th style="text-align: center;">Qté</th>
                <th style="text-align: right;">Prix Unit.</th>
                <th style="text-align: right;">Remise</th>
                <th style="text-align: right;">Total Ligne</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->items as $item)
            <tr>
                <td>
                    <strong>{{ $item->product->name }}</strong><br>
                    <small style="color:#6b7280;">{{ $item->product->sku }}</small>
                </td>
                <td style="text-align: center;">{{ $item->quantity }}</td>
                <td style="text-align: right;">{{ number_format($item->unit_price, 0, ',', ' ') }}</td>
                <td style="text-align: right;">{{ number_format($item->discount, 0, ',', ' ') }}</td>
                <td style="text-align: right;">{{ number_format($item->total_price, 0, ',', ' ') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div class="total-row">
            <span class="label">Sous-total HT</span>
            <span class="value">{{ number_format($sale->subtotal, 0, ',', ' ') }} FCFA</span>
        </div>
        @if($sale->discount_amount > 0)
        <div class="total-row">
            <span class="label">Remise globale</span>
            <span class="value">- {{ number_format($sale->discount_amount, 0, ',', ' ') }} FCFA</span>
        </div>
        @endif
        @if($sale->has_tax)
        <div class="total-row">
            <span class="label">TVA (18%)</span>
            <span class="value">{{ number_format($sale->tax_amount, 0, ',', ' ') }} FCFA</span>
        </div>
        @endif
        <div class="total-row grand-total">
            <span>NET À PAYER</span>
            <span class="value">{{ number_format($sale->total_amount, 0, ',', ' ') }} FCFA</span>
        </div>
        <p style="font-size: 11px; color:#6b7280; margin-top:10px;">
            Arrêté la présente facture à la somme de : <br>
            <strong>{{ $amountInWords }} Francs CFA</strong>
        </p>
    </div>

    <div class="footer">
        {{ $agency->name }} - @if($agency->capital) S.A.R.L au capital de {{ number_format($agency->capital, 0, ',', ' ') }} FCFA @endif<br>
        Merci de votre confiance !
    </div>
</body>
</html>
