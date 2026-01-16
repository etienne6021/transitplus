<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Agency;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\SydoniaXmlService;

class SaleController extends Controller
{
    public function generatePdf(Sale $sale)
    {
        $agency = auth()->user()->agency ?? Agency::first();
        
        $logoBase64 = null;
        if ($agency && $agency->logo) {
            $path = storage_path('app/public/' . $agency->logo);
            if (file_exists($path)) {
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $dataImg = file_get_contents($path);
                $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($dataImg);
            }
        }
        
        $data = [
            'sale' => $sale,
            'agency' => $agency,
            'logoBase64' => $logoBase64,
            'amountInWords' => SydoniaXmlService::numberToWords($sale->total_amount)
        ];

        $pdf = Pdf::loadView('pdf.sale', $data);
        
        return $pdf->download('Facture_' . $sale->number . '.pdf');
    }
}
