<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductFilterRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Requests\UploadProductRequest;
use App\Services\LogService;
use App\Services\ProductService;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    public function __construct(private ProductService $productService, private LogService $logService)
    {
    }
    public function index(ProductFilterRequest $request)
    {
        $products = $this->productService->getProducts($request);

        // Extract variables from the request
        $sku = $request->input('sku');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $sortField = $request->input('sort_field', 'id');
        $sortDirection = $request->input('sort_direction', 'asc');
        $perPage = $request->input('per_page', 25);

        // Log the action
        $this->logService->logAction('View Products', 'Products page viewed.');

        return view('products.index', compact('products', 'sku', 'startDate', 'endDate', 'sortField', 'sortDirection', 'perPage'));
    }

    public function uploadfile()
    {
        return view('products.upload');
    }
    public function upload(UploadProductRequest $request)
    {
        try {
            $file = $request->file('csv_file');
            $data = Excel::toArray([], $file)[0] ?? [];

            $this->productService->uploadProducts($data);

            $this->logService->logAction('Upload CSV Successful', 'CSV file processed successfully.');
            return back()->with('success', 'CSV file processed successfully.');
        } catch (\Exception $e) {
            $this->logService->logAction('Upload CSV Failed', $e->getMessage());
            return back()->withErrors(['csv_file' => $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        try {
            $product = $this->productService->findProduct($id);
            $this->logService->logAction('Edit Product', "Edit product page viewed for product ID: {$id}");
            return view('products.edit', compact('product'));
        } catch (\Exception $e) {
            $this->logService->logAction('Edit Product Failed', $e->getMessage());
            abort(404);
        }
    }

    public function update(UpdateProductRequest $request, $id)
    {
        try {
            $product = $this->productService->updateProduct($request, $id);

            $this->logService->logAction('Update Product', "Product updated with ID: {$id}");
            return redirect()->route('products.index')->with('success', 'Product updated successfully.');
        } catch (\Exception $e) {
            $this->logService->logAction('Update Product Failed', $e->getMessage());
            abort(404);
        }
    }

    public function apiIndex(ProductFilterRequest $request)
    {
        $products = $this->productService->getProducts($request);
        return response()->json($products);
    }

    public function apiUpdate(UpdateProductRequest $request, $id)
    {
        try {
            if (Auth::user()->role === 'super admin' || Auth::user()->role === 'admin') {
                $product = $this->productService->updateProduct($request, $id);
                return response()->json(['message' => 'Product updated successfully.']);
            } else {
                return response()->json(['message' => 'User not Authorized!']);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Product not found.'], 404);
        }
    }
}