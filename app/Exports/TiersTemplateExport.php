<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TiersTemplateExport implements FromArray, WithHeadings
{
    /**
     * Provide an empty data set for the template.
     */
    public function array(): array
    {
        return []; // Empty data
    }

    /**
     * Define the headers for the template file.
     */
    public function headings(): array
    {
        return ['sku', 'tier_name', 'min_quantity', 'max_quantity', 'value', 'price_type', 'customer_group', 'type'];
    }
}
