<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Http\Requests\ProductFilterRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Requests\UploadProductRequest;
use App\Models\Product;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    public function index(ProductFilterRequest $request)
    {
        $query = Product::query();
        $sku = $request->input('sku');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : null;
        $sortField = $request->input('sort_field', 'id');
        $sortDirection = $request->input('sort_direction', 'asc');
        $perPage = $request->input('per_page', 25); // Default to 25 items per page if not provided

        if ($sku) {
            $query->where('sku', 'like', '%' . $sku . '%');
        }

        if ($startDate && $endDate) {
            $query->whereBetween('updated_at', [$startDate, $endDate]);
        }

        $products = $query->orderBy($sortField, $sortDirection)->paginate($perPage);

        LogHelper::logAction('View Products', 'Products page viewed.');

        return view('products.index', compact('products', 'sku', 'startDate', 'endDate', 'sortField', 'sortDirection', 'perPage'));
    }

    public function upload(UploadProductRequest $request)
    {
        $file = $request->file('csv_file');
        $data = Excel::toArray([], $file);

        if (isset($data[0])) {
            foreach ($data[0] as $index => $row) {
                if ($index === 0) {
                    continue; // Skip header row
                }

                if (isset($row[0]) && isset($row[1]) && isset($row[2]) && isset($row[3])) {
                    // Check if the record exists by SKU or Item Code
                    $existingProduct = Product::where('sku', $row[1])->orWhere('item_code', $row[0])->first();

                    if ($existingProduct) {
                        // Update the existing product
                        $existingProduct->update([
                            'sku' => $row[1],
                            'item_code' => $row[0],
                            'price' => $row[2],
                            'stock' => $row[3],
                        ]);
                    } else {
                        // Create a new product
                        Product::create([
                            'sku' => $row[1],
                            'item_code' => $row[0],
                            'price' => $row[2],
                            'stock' => $row[3],
                        ]);
                    }
                } else {
                    LogHelper::logAction('Upload CSV Failed', 'Invalid CSV format.');
                    return back()->withErrors(['csv_file' => 'Invalid CSV format.']);
                }
            }

            LogHelper::logAction('Upload CSV Successful', 'CSV file processed successfully.');
            return back()->with('success', 'CSV file processed successfully.');
        } else {
            LogHelper::logAction('Upload CSV Failed', 'No data found in the CSV file.');
            return back()->withErrors(['csv_file' => 'No data found in the CSV file.']);
        }
    }

    public function edit($id)
    {
        try {
            $product = Product::findOrFail($id);
            LogHelper::logAction('Edit Product', "Edit product page viewed for product ID: {$id}");
            return view('products.edit', compact('product'));
        } catch (\Exception $e) {
            LogHelper::logAction('Edit Product Failed', "Product not found with ID: {$id}");
            abort(404);
        }
    }

    public function update(UpdateProductRequest $request, $id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->update($request->validated());

            LogHelper::logAction('Update Product', "Product updated with ID: {$id}");

            return redirect()->route('products.index')->with('success', 'Product updated successfully.');
        } catch (\Exception $e) {
            LogHelper::logAction('Update Product Failed', "Failed to update product with ID: {$id}. Error: {$e->getMessage()}");
            abort(404);
        }
    }
}