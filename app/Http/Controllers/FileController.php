<?php

namespace App\Http\Controllers;

use App\Exports\ProductsExport;
use App\Exports\ProductsTemplateExport;
use App\Exports\TiersExport;
use App\Exports\TiersTemplateExport;
use Maatwebsite\Excel\Facades\Excel;

class FileController extends Controller
{
    /**
     * Download Products Template.
     */
    public function downloadProductsTemplate()
    {
        $fileName = 'Products_Template.xlsx';
        return Excel::download(new ProductsTemplateExport, $fileName);
    }

    /**
     * Download Tiers Template.
     */
    public function downloadTiersTemplate()
    {
        $fileName = 'Tiers_Template.xlsx';
        return Excel::download(new TiersTemplateExport, $fileName);
    }
    /**
     * Export products data.
     */
    public function exportProducts()
    {
        return Excel::download(new ProductsExport, 'products.xlsx');
    }

    /**
     * Export tiers data.
     */
    public function exportTiers()
    {
        return Excel::download(new TiersExport, 'tiers.xlsx');
    }
}
