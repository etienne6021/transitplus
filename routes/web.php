<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/invoice/{invoice}/print', function (\App\Models\Invoice $invoice) {
    $agency = auth()->user()->agency ?? \App\Models\Agency::first();
    $logoBase64 = null;
    if ($agency && $agency->logo) {
        $path = storage_path('app/public/' . $agency->logo);
        if (file_exists($path)) {
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode(file_get_contents($path));
        }
    }
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.invoice', compact('invoice', 'agency', 'logoBase64'));
    return $pdf->stream('Facture-' . $invoice->number . '.pdf');
})->name('invoice.print')->middleware(['auth']);

Route::get('/admin/quotations/{quotation}/pdf', function (App\Models\Quotation $quotation) {
    $agency = $quotation->agency ?? App\Models\Agency::first();
    $logoBase64 = null;
    if ($agency && $agency->logo) {
        $path = storage_path('app/public/' . $agency->logo);
        if (file_exists($path)) {
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode(file_get_contents($path));
        }
    }
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.quotation', compact('quotation', 'agency', 'logoBase64'));
    return $pdf->stream('PROFORMA-' . $quotation->number . '.pdf');
})->name('quotation.pdf')->middleware(['auth']);

Route::get('/admin/sales/{sale}/pdf', function (App\Models\Sale $sale) {
    $agency = $sale->agency ?? App\Models\Agency::first();
    $logoBase64 = null;
    if ($agency && $agency->logo) {
        $path = storage_path('app/public/' . $agency->logo);
        if (file_exists($path)) {
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode(file_get_contents($path));
        }
    }
    $amountInWords = App\Services\SydoniaXmlService::numberToWords($sale->total_amount);
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.sale', compact('sale', 'agency', 'amountInWords', 'logoBase64'));
    return $pdf->stream('FACTURE-' . $sale->number . '.pdf');
})->name('sale.pdf')->middleware(['auth']);

Route::get('/admin/payrolls/{payroll}/pdf', function (App\Models\Payroll $payroll) {
    $agency = $payroll->employee->agency ?? App\Models\Agency::first();
    $logoBase64 = null;
    if ($agency && $agency->logo) {
        $path = storage_path('app/public/' . $agency->logo);
        if (file_exists($path)) {
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode(file_get_contents($path));
        }
    }
    $amountInWords = App\Services\SydoniaXmlService::numberToWords($payroll->net_salary);
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.payroll', compact('payroll', 'agency', 'amountInWords', 'logoBase64'));
    return $pdf->stream('Bulletin-' . $payroll->employee->last_name . '-' . $payroll->month . '-' . $payroll->year . '.pdf');
})->name('payroll.pdf')->middleware(['auth']);
