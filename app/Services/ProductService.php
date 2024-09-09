<?php

namespace App\Services;

use App\Models\Product;
use Carbon\Carbon;

class ProductService
{
    public function getProducts($request)
    {
        $query = Product::query();
        $sku = $request->input('sku');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : null;
        $sortField = $request->input('sort_field', 'id');
        $sortDirection = $request->input('sort_direction', 'asc');
        $perPage = $request->input('per_page', 25);

        if ($sku) {
            $query->where('sku', 'like', '%' . $sku . '%');
        }

        if ($startDate && $endDate) {
            $query->whereBetween('updated_at', [$startDate, $endDate]);
        }

        return $query->orderBy($sortField, $sortDirection)->paginate($perPage);
    }

    public function uploadProducts($data)
    {
        foreach ($data as $index => $row) {
            if ($index === 0) {
                continue; // Skip header row
            }

            if (isset($row[0]) && isset($row[1]) && isset($row[2]) && isset($row[3])) {
                $existingProduct = Product::where('sku', $row[1])->orWhere('item_code', $row[0])->first();

                if ($existingProduct) {
                    $existingProduct->update([
                        'sku' => $row[1],
                        'item_code' => $row[0],
                        'price' => $row[2],
                        'stock' => $row[3],
                    ]);
                } else {
                    Product::create([
                        'sku' => $row[1],
                        'item_code' => $row[0],
                        'price' => $row[2],
                        'stock' => $row[3],
                    ]);
                }
            } else {
                throw new \Exception('Invalid CSV format.');
            }
        }
    }

    public function findProduct($id)
    {
        return Product::findOrFail($id);
    }

    public function updateProduct($request, $id)
    {

        $product = $this->findProduct($id);
        $product->update($request->validated());
        return $product;
    }
}
