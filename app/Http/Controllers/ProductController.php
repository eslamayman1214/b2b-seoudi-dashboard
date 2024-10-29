<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductFilterRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Requests\UploadProductRequest;
use App\Http\Requests\UploadTierRequest;
use App\Models\Configuration;
use App\Models\CustomerGroup;
use App\Models\Product;
use App\Models\ProductVersion;
use App\Models\Tier;
use App\Services\LogService;
use App\Services\ProductService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    public function __construct(private ProductService $productService, private LogService $logService)
    {
    }
    public function index(ProductFilterRequest $request)
    {
        // Extract variables from the request
        $sku = $request->input('sku');

        // If the user submitted the form but didn't provide start or end dates, use default values
        $startDate = $request->filled('start_date') ? $request->input('start_date') : null;
        $endDate = $request->filled('end_date') ? $request->input('end_date') : null;

        // Apply defaults only if the user hits the filter and leaves date fields empty
        if ($request->isMethod('get') && $request->has('sku')) {
            $startDate = $startDate ?? '2024-09-01'; // Default to 1st September if not provided
            $endDate = $endDate ?? now()->format('Y-m-d'); // Default to today if not provided
        }

        $sortField = $request->input('sort_field', 'id');
        $sortDirection = $request->input('sort_direction', 'asc');
        $perPage = $request->input('per_page', 25);

        // Log the action
        $this->logService->logAction('View Products', 'Products page viewed.');

        // Merge the default values for start and end dates into the request if needed
        $products = $this->productService->getProducts($request->merge([
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]));

        // Return view with all variables, without setting default date values in input fields
        return view('products.index', compact('products', 'sku', 'startDate', 'endDate', 'sortField', 'sortDirection', 'perPage'));
    }
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'item_code' => 'required|string|max:255|unique:products,item_code,',
            'sku' => 'required|string|max:255|unique:products,sku,',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
        ]);

        try {
            DB::beginTransaction();

            $existingProduct = Product::where('sku', $validatedData['sku'])
                ->orWhere('item_code', $validatedData['item_code'])
                ->first();

            if ($existingProduct) {
                throw new Exception('This product already exists.');

            }
            $product = Product::create($validatedData);

            $versionData = array_merge($validatedData, [
                'product_id' => $product->id,
                'tiers' => $product->tiers ?? [],
            ]);
            ProductVersion::create($versionData);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Product saved successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to save the product.', 'error' => $e->getMessage()], 500);
        }
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
            Log::info('Received update request for product ID: ' . $id);
            Log::info('Request data:', $request->all());

            $changesDetected = false;

            // Update the main product details
            $product = $this->productService->updateProduct($request, $id);

            // Check if main product details were changed
            if ($product->wasChanged()) {
                $changesDetected = true;
            }

            // Handle tiers update
            $tiersData = $request->input('tiers', []);
            Log::info('Tiers data:', $tiersData);

            // Validate tiers
            $this->validateTiers($tiersData, $product);

            // Get existing tier IDs
            $existingTierIds = [];
            foreach ($tiersData as $customerGroup => $tiers) {
                $existingTierIds = array_merge($existingTierIds, array_filter(array_column($tiers, 'id')));
            }

            // Remove tiers not in the request
            $deletedTiers = $product->tiers()->whereNotIn('id', $existingTierIds)->delete();
            if ($deletedTiers > 0) {
                $changesDetected = true;
            }

            // Update or create tiers
            foreach ($tiersData as $customerGroup => &$tiers) {
                foreach ($tiers as $index => &$tierData) {
                    Log::info("Processing tier for customer group: $customerGroup, index: $index");
                    Log::info('Tier data:', $tierData);

                    $tierData['customer_group'] = $customerGroup;

                    if (isset($tierData['id']) && $tierData['id']) {
                        $tier = Tier::find($tierData['id']);
                        if ($tier) {
                            // If price_type is not in the request, use the existing one from the database
                            if (!isset($tierData['price_type'])) {
                                $tierData['price_type'] = $tier->price_type;
                                // Update the $tiersData array as well
                                $tiers[$index]['price_type'] = $tier->price_type;
                            }
                            $originalTier = $tier->getOriginal();
                            $tier->update($tierData);
                            if ($tier->wasChanged()) {
                                $changesDetected = true;
                            }
                            Log::info("Updated existing tier: " . $tier->id);
                        } else {
                            throw new \Exception("Tier with ID {$tierData['id']} not found.");
                        }
                    } else {
                        // For new tiers, ensure price_type is set (default to 'range' if not provided)
                        $tierData['price_type'] = $tierData['price_type'] ?? 'range';
                        // Update the $tiersData array as well
                        $tiers[$index]['price_type'] = $tierData['price_type'];
                        $newTier = $product->tiers()->create($tierData);
                        $changesDetected = true;
                        Log::info("Created new tier: " . $newTier->id);
                    }
                }
            }
            // Update the updated_at timestamp if changes were detected
            if ($changesDetected) {
                $product->touch();
            }
            DB::commit();

            // Log and redirect
            $this->logService->logAction('Update Product', "Product updated with ID: {$id}");

            if ($changesDetected) {
                return back()->with('success', 'Product updated successfully.');
            } else {
                return back()->with('info', 'No changes detected.');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating product: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            $this->logService->logAction('Update Product Failed', $e->getMessage());
            return back()->with('fail', 'An error occurred while updating the product: ' . $e->getMessage());
        }
    }

    private function validateTiers(array $tiersData, $product)
    {
        foreach ($tiersData as $customerGroup => $tiers) {
            $hasFixedTier = false;

            foreach ($tiers as $index => $tierData) {
                Log::info("Validating tier for customer group: $customerGroup, index: $index");
                Log::info('Tier data:', $tierData);

                // If updating an existing tier, get the price_type from the database if not provided
                if (isset($tierData['id'])) {
                    $existingTier = $product->tiers()->find($tierData['id']);
                    if ($existingTier) {
                        $tierData['price_type'] = $tierData['price_type'] ?? $existingTier->price_type;
                    }
                } else {
                    // For new tiers, default to 'range' if not provided
                    $tierData['price_type'] = $tierData['price_type'] ?? 'range';
                }

                if (!isset($tierData['price_type'])) {
                    throw new \Exception("Price type is not set for tier {$index} in customer group {$customerGroup}.");
                }

                // Check if a fixed price tier already exists
                if ($tierData['price_type'] === 'fixed') {
                    if ($hasFixedTier) {
                        throw new \Exception("Only one fixed price tier is allowed for customer group {$customerGroup}.");
                    }
                    $hasFixedTier = true;

                    // Nullify the quantities for fixed price type
                    $tierData['min_quantity'] = null;
                    $tierData['max_quantity'] = null;
                    continue;
                }

                // Validate for 'range' price type
                if ($tierData['price_type'] === 'range' && (!isset($tierData['min_quantity']) || !isset($tierData['max_quantity']) || $tierData['max_quantity'] <= $tierData['min_quantity'])) {
                    throw new \Exception("Invalid quantities in tier {$index} for customer group {$customerGroup}. Max quantity must be greater than Min quantity for range tiers.");
                }
                if ($tierData['value'] <= 0) {
                    throw new \Exception("Invalid value in tier {$index} for customer group {$customerGroup}. Value must be greater than Zero!");
                }
            }
        }
    }

    public function fetchCustomerGroups()
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

            // Iterate over customer groups from the API response
            foreach ($body['items'] as $group) {
                // Store customer groups in the database if they don’t exist
                CustomerGroup::firstOrCreate(
                    ['code' => strtoupper($group['code'])], // Store 'code' as 'code'
                    ['group_id' => $group['id']]// Store 'id' as 'group_id'
                );
            }

            return $body['items'];

        } catch (\Exception $e) {
            Log::error('Failed to fetch customer groups: ' . $e->getMessage());
            return [];
        }
    }
    public function uploadTiers(UploadTierRequest $request)
    {
        try {
            $file = $request->file('tiers_csv_file');
            $data = Excel::toArray([], $file)[0] ?? [];

            $this->productService->uploadTiers($data);

            $this->logService->logAction('Upload Tiers CSV Successful', 'CSV file processed successfully.');
            return back()->with('success', 'CSV file processed successfully.');
        } catch (\Exception $e) {
            $this->logService->logAction('Upload Tiers CSV Failed', $e->getMessage());
            return back()->withErrors(['tiers_csv_file' => $e->getMessage()]);
        }
    }
}