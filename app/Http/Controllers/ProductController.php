<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductFilterRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Requests\UploadProductRequest;
use App\Models\Configuration;
use App\Models\CustomerGroup;
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

            // Check if customer groups are already stored in the database
            $existingCustomerGroups = CustomerGroup::count();

            if ($existingCustomerGroups === 0) {
                // If no customer groups are stored, fetch from API and store in the database
                $customerGroups = $this->fetchCustomerGroups();

            }
            // Fetch customer groups from the database
            $customerGroups = CustomerGroup::pluck('code');

            $this->logService->logAction('Edit Product', "Edit product page viewed for product ID: {$id}");
            return view('products.edit', compact('product', 'tiers', 'customerGroups'));
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
            return back()->with('success', 'Product updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->logService->logAction('Update Product Failed', $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'An error occurred while updating the product.']);
        }
    }

    private function validateTiers(array $tiersData)
    {
        $hasFixedTier = false;

        foreach ($tiersData as $index => $tierData) {
            // Check if a fixed price tier already exists
            if ($tierData['price_type'] === 'fixed') {
                if ($hasFixedTier) {
                    throw new \Exception('Only one fixed price tier is allowed.');
                }
                $hasFixedTier = true;

                // Nullify the quantities for fixed price type
                $tierData['min_quantity'] = null;
                $tierData['max_quantity'] = null;
                continue;
            }

            // Validate for 'range' price type
            if (!isset($tierData['min_quantity']) || !isset($tierData['max_quantity']) || $tierData['max_quantity'] <= $tierData['min_quantity']) {
                throw new \Exception("Invalid quantities in tier $index. Max quantity must be greater than Min quantity for range tiers.");
            }
        }
    }

    private function fetchCustomerGroups()
    {
        try {
            // Fetch the configuration values from the Configuration model
            $customerEndpoint = Configuration::getValueByKey('customer_endpoint');
            $customerToken = Configuration::getValueByKey('customer_token');
            $baseUrl = Configuration::getValueByKey('base_url');
            $fullUrl = $baseUrl . $customerEndpoint;

            $client = new \GuzzleHttp\Client();
            $response = $client->get($fullUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $customerToken,
                    'Accept' => 'application/json',
                ],
                'verify' => false, // Disable SSL certificate verification
            ]);

            $statusCode = $response->getStatusCode();
            if ($statusCode !== 200) {
                throw new \Exception("API request failed with status code $statusCode");
            }

            $body = json_decode($response->getBody(), true);
            if (!isset($body['items']) || !is_array($body['items'])) {
                throw new \Exception("Unexpected response structure from API");
            }

            $customerGroups = array_unique(array_column($body['items'], 'code'));

            // Store customer groups in the database if they don’t exist
            foreach ($customerGroups as $groupCode) {
                CustomerGroup::firstOrCreate(['code' => $groupCode]);
            }

            return $customerGroups;

        } catch (\Exception $e) {
            \Log::error('Failed to fetch customer groups: ' . $e->getMessage());
            return [];
        }
    }

}