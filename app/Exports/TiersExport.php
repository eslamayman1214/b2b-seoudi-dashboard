<?php

namespace App\Exports;

use App\Models\Tier;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TiersExport implements FromCollection, WithHeadings
{
    /**
     * Fetch the data to be exported.
     * This method replaces the product_id column with the SKU from the Product model.
     */
    public function collection()
    {
        // Eager load the related Product and map the data
        return Tier::with('product') // Load the Product relationship
            ->get()
            ->map(function ($tier) {
                return [
                    'sku' => $tier->product->sku, // Replace product_id with sku, fallback to 'N/A' if no product
                    'tier_name' => $tier->tier_name,
                    'min_quantity' => $tier->min_quantity,
                    'max_quantity' => $tier->max_quantity,
                    'value' => $tier->value,
                    'price_type' => $tier->price_type,
                    'customer_group' => $tier->customer_group,
                    'type' => $tier->type,
                ];
            });
    }

    /**
     * Define the header row.
     * This replaces product_id with sku.
     */
    public function headings(): array
    {
        return [
            'sku',
            'tier_name',
            'min_quantity',
            'max_quantity',
            'value',
            'price_type',
            'customer_group',
            'type',
        ];
    }
}
