<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();
        $sku = $request->input('sku');

        // Filter by SKU if search parameter is provided
        if ($request->has('sku')) {
            $query->where('sku', 'like', '%' . $request->sku . '%');
        }

        // Paginate the results with 50 items per page
        $products = $query->paginate(50);

        return view('products.index', compact('products', 'sku'));
    }

    public function upload(Request $request)
    {
        $request->validate(['csv_file' => 'required|mimes:csv,txt']);

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
        $product = Product::findOrFail($id);
        return view('products.edit', compact('product'));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'item_code' => 'required|string|max:255|unique:products,item_code,' . $id,
            'sku' => 'required|string|max:255|unique:products,sku,' . $id,
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
        ]);

        $product = Product::findOrFail($id);
        $product->update($request->all());

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }
}