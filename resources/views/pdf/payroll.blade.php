<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bulletin de Paie - {{ $payroll->employee->full_name }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #333; line-height: 1.4; margin: 0; padding: 20px; }
        .header { margin-bottom: 20px; border-bottom: 2px solid #2563eb; padding-bottom: 10px; }
        .logo { max-height: 60px; }
        .company-info { float: right; text-align: right; width: 50%; }
        .title { text-align: center; background: #f3f4f6; padding: 10px; font-size: 16px; font-weight: bold; text-transform: uppercase; margin: 20px 0; border: 1px solid #d1d5db; }
        .grid { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .grid th, .grid td { border: 1px solid #e5e7eb; padding: 8px; text-align: left; }
        .grid th { background-color: #f9fafb; font-weight: bold; }
        .info-section { margin-bottom: 20px; }
        .info-box { border: 1px solid #e5e7eb; padding: 10px; border-radius: 4px; }
        .info-title { font-weight: bold; color: #2563eb; margin-bottom: 5px; border-bottom: 1px solid #e5e7eb; display: inline-block; }
        .summary-box { float: right; width: 300px; margin-top: 20px; }
        .total-row { background: #2563eb; color: white; font-weight: bold; }
        .footer { position: fixed; bottom: 30px; left: 0; right: 0; text-align: center; font-size: 9px; color: #6b7280; border-top: 1px solid #e5e7eb; padding-top: 10px; }
        .signature-area { margin-top: 50px; }
        .signature-box { width: 45%; display: inline-block; text-align: center; border-top: 1px solid #333; padding-top: 10px; margin-top: 40px; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }
    </style>
</head>
<body>

    <div class="header">
        @if($logoBase64)
            <img src="{{ $logoBase64 }}" class="logo">
        @else
            <div style="font-size: 24px; font-weight: bold; color: #2563eb;">{{ $agency->name ?? 'TRANSIT PLUS' }}</div>
        @endif
        <div class="company-info">
            <p style="margin:0; font-weight:bold;">{{ $agency->name }}</p>
            <p style="margin:0;">{{ $agency->address }}</p>
            <p style="margin:0;">Tel: {{ $agency->contact_phone }} | Email: {{ $agency->email }}</p>
            @if($agency->capital)<p style="margin:0;">Capital Social: {{ number_format($agency->capital, 0, ',', ' ') }} FCFA</p>@endif
        </div>
        <div style="clear: both;"></div>
    </div>

    <div class="title">Bulletin de Paie - {{ \Carbon\Carbon::create(null, $payroll->month)->translatedFormat('F') }} {{ $payroll->year }}</div>

    <div class="info-section">
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%; vertical-align: top; padding-right: 10px;">
                    <div class="info-box">
                        <span class="info-title">DÉTAILS EMPLOYÉ</span><br>
                        <strong>{{ $payroll->employee->full_name }}</strong><br>
                        Poste: {{ $payroll->employee->position }}<br>
                        Contrat: {{ $payroll->employee->contract_type }}<br>
                        N° CNSS: {{ $payroll->employee->cnss_number ?? 'N/A' }}<br>
                        Arrivée: {{ $payroll->employee->hire_date?->format('d/m/Y') }}<br>
                    </div>
                </td>
                <td style="width: 50%; vertical-align: top; padding-left: 10px;">
                    <div class="info-box">
                        <span class="info-title">PÉRIODE DE PAIE</span><br>
                        Mois: {{ \Carbon\Carbon::create(null, $payroll->month)->translatedFormat('F') }}<br>
                        Année: {{ $payroll->year }}<br>
                        Paiement: Par virement bancaire<br>
                        Date de génération: {{ now()->format('d/m/Y') }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <table class="grid">
        <thead>
            <tr>
                <th>DÉSIGNATION DES ÉLÉMENTS</th>
                <th style="width: 100px; text-align: center;">TAUX</th>
                <th style="width: 120px; text-align: right;">GAINS (Part Salariale)</th>
                <th style="width: 120px; text-align: right;">RETENUES</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Salaire de base de l'agent</td>
                <td style="text-align: center;">-</td>
                <td class="text-right">{{ number_format($payroll->base_salary, 0, ',', ' ') }}</td>
                <td></td>
            </tr>
            @if($payroll->bonuses > 0)
            <tr>
                <td>Primes de rendement et gratifications</td>
                <td style="text-align: center;">-</td>
                <td class="text-right">{{ number_format($payroll->bonuses, 0, ',', ' ') }}</td>
                <td></td>
            </tr>
            @endif
            @if($payroll->transport_allowance > 0)
            <tr>
                <td>Indemnité forfaitaire de transport</td>
                <td style="text-align: center;">-</td>
                <td class="text-right">{{ number_format($payroll->transport_allowance, 0, ',', ' ') }}</td>
                <td></td>
            </tr>
            @endif
            <tr style="background-color: #fefce8; font-weight: bold;">
                <td>TOTAL SALAIRE BRUT</td>
                <td style="text-align: center;">-</td>
                <td class="text-right">{{ number_format($payroll->brut_salary, 0, ',', ' ') }}</td>
                <td></td>
            </tr>
            <tr>
                <td>Cotisation CNSS (Part Ouvrière)</td>
                <td style="text-align: center;">4%</td>
                <td></td>
                <td class="text-right">{{ number_format($payroll->cnss_employee, 0, ',', ' ') }}</td>
            </tr>
            @if($payroll->irpp > 0)
            <tr>
                <td>Impôt sur le Revenu (IRPP)</td>
                <td style="text-align: center;">-</td>
                <td></td>
                <td class="text-right">{{ number_format($payroll->irpp, 0, ',', ' ') }}</td>
            </tr>
            @endif
            @if($payroll->deductions > 0)
            <tr>
                <td>Autres retenues (Avances, prélèvements)</td>
                <td style="text-align: center;">-</td>
                <td></td>
                <td class="text-right">{{ number_format($payroll->deductions, 0, ',', ' ') }}</td>
            </tr>
            @endif
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="2" style="text-align: right; padding: 12px; font-size: 14px;">NET À PAYER (FCFA)</td>
                <td colspan="2" style="text-align: right; padding: 12px; font-size: 16px;">{{ number_format($payroll->net_salary, 0, ',', ' ') }}</td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 10px;">
        <p>Arrêté le présent bulletin de paie à la somme de : <br>
        <strong style="text-transform: uppercase;">{{ $amountInWords }} FRANCS CFA</strong></p>
    </div>

    <div class="signature-area">
        <div class="signature-box" style="float: left;">
            Signature de l'Employé
        </div>
        <div class="signature-box" style="float: right;">
            La Direction
        </div>
        <div style="clear: both;"></div>
    </div>

    <div class="footer">
        {{ $agency->name }} - {{ $agency->address }} - {{ $agency->contact_phone }}<br>
        Un document certifié conforme par le système de gestion TRANSIT PLUS
    </div>

</body>
</html>
