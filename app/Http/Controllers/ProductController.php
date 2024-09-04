<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductFilterRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Requests\UploadProductRequest;
use App\Models\Tier;
use App\Services\LogService;
use App\Services\ProductService;
use Illuminate\Support\Facades\DB;
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
            $tiers = $product->tiers; // Get tiers for the product
            $this->logService->logAction('Edit Product', "Edit product page viewed for product ID: {$id}");
            return view('products.edit', compact('product', 'tiers'));
        } catch (\Exception $e) {
            $this->logService->logAction('Edit Product Failed', $e->getMessage());
            abort(404);
        }
    }

    public function update(UpdateProductRequest $request, $id)
    {
        DB::beginTransaction();

        try {
            // Update the main product details
            $product = $this->productService->updateProduct($request, $id);

            // Handle tiers update
            $tiersData = $request->input('tiers', []);

            // Validate tiers
            $this->validateTiers($tiersData);

            // Get existing tier IDs
            $existingTierIds = array_filter(array_column($tiersData, 'id'));

            // Remove tiers not in the request
            $product->tiers()->whereNotIn('id', $existingTierIds)->delete();

            // Update or create tiers
            foreach ($tiersData as $tierData) {
                if (isset($tierData['id'])) {
                    $tier = Tier::find($tierData['id']);
                    if ($tier) {
                        $tier->update($tierData);
                    } else {
                        throw new \Exception("Tier with ID {$tierData['id']} not found.");
                    }
                } else {
                    $product->tiers()->create($tierData);
                }
            }

            DB::commit();

            // Log and redirect
            $this->logService->logAction('Update Product', "Product updated with ID: {$id}");
            return redirect()->route('products.index')->with('success', 'Product updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->logService->logAction('Update Product Failed', $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'An error occurred while updating the product.']);
        }
    }

    private function validateTiers(array $tiersData)
    {
        foreach ($tiersData as $tierData) {
            if (!isset($tierData['min_quantity']) || !isset($tierData['max_quantity']) || $tierData['max_quantity'] <= $tierData['min_quantity']) {
                throw new \Exception('Invalid tier quantities.');
            }
        }
    }
}
