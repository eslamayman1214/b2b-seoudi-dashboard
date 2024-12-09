<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductsExport implements FromCollection, WithHeadings
{
    /**
     * Define the data to be exported.
     */
    public function collection()
    {
        // Fetch only the necessary columns in the specified order
        return Product::select(['item_code', 'sku', 'price', 'stock'])->get();
    }

    /**
     * Customize the header row.
     */
    public function headings(): array
    {
        return ['item_code', 'sku', 'price', 'stock'];
    }
}
