<?php

namespace App\Http\Controllers;

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

        if ($sku) {
            $query->where('sku', 'like', '%' . $sku . '%');
        }

        if ($startDate && $endDate) {
            $query->whereBetween('updated_at', [$startDate, $endDate]);
        }

        $products = $query->orderBy($sortField, $sortDirection)->paginate(50);

        return view('products.index', compact('products', 'sku', 'startDate', 'endDate', 'sortField', 'sortDirection'));
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
                    return back()->withErrors(['csv_file' => 'Invalid CSV format.']);
                }
            }

            return back()->with('success', 'CSV file processed successfully.');
        } else {
            return back()->withErrors(['csv_file' => 'No data found in the CSV file.']);
        }
    }

    public function edit($id)
    {
        try {
            $product = Product::findOrFail($id);
            return view('products.edit', compact('product'));
        } catch (\Exception $e) {
            abort(404);
            // Redirect to your custom 404 page route
        }
    }
    public function update(UpdateProductRequest $request, $id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->update($request->validated());

            return redirect()->route('products.index')->with('success', 'Product updated successfully.');
        } catch (\Exception $e) {
            abort(404);
        }
    }
}