<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductFilterRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Configuration;
use App\Models\CronLog;
use App\Models\CustomerGroup;
use App\Models\Product;
use App\Models\ProductVersion;
use App\Services\ProductService;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function __construct(private ProductService $productService)
    {
    }

    public function index(ProductFilterRequest $request): JsonResponse
    {
        try {
            $products = $this->productService->getProducts($request);
            return response()->json(['success' => true, 'data' => $products]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error fetching products: ' . $e->getMessage()], 500);
        }
    }

    public function update(UpdateProductRequest $request, $id): JsonResponse
    {
        try {
            if (Auth::user()->role === 'super admin' || Auth::user()->role === 'admin') {
                $product = $this->productService->updateProduct($request, $id);
                return response()->json(['success' => true, 'message' => 'Product updated successfully.']);
            } else {
                return response()->json(['success' => false, 'message' => 'User not authorized.'], 403);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error updating product: ' . $e->getMessage()], 500);
        }
    }

    public function getAllProductsWithTiers(): JsonResponse
    {
        try {
            if (Auth::user()->role === 'super admin' || Auth::user()->role === 'admin') {
                // Fetch products with their tiers and associated customer group
                $products = Product::with(['tiers.customerGroup'])->get();

                // Map the response to include both customer_group code and customer_group_id
                $productsWithCustomerGroupCode = $products->map(function ($product) {
                    $product->tiers->map(function ($tier) {
                        // Replace the full customerGroup object with only 'code' and 'group_id'
                        $tier->customer_group = $tier->customerGroup ? $tier->customerGroup->code : null;
                        $tier->customer_group_id = $tier->customerGroup ? $tier->customerGroup->group_id : null;
                        unset($tier->customerGroup); // Remove the full customerGroup object
                        return $tier;
                    });
                    return $product;
                });

                return response()->json(['success' => true, 'data' => $productsWithCustomerGroupCode]);
            } else {
                return response()->json(['success' => false, 'message' => 'User not authorized.'], 403);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error fetching products with tiers: ' . $e->getMessage()], 500);
        }
    }
    public function sendProductDataToApi()
    {
        // Fetch all the edited products from the ProductVersion table
        $editedProducts = ProductVersion::all(); // No need for the 'with' method since 'tiers' is JSON

        // Check if there are any products to send
        if ($editedProducts->isEmpty()) {
            Log::info('No edited products to send.');
            return response()->json(['message' => 'No edited products to send.'], 204);
        }

        // Initialize the payload array
        $payload = ['products' => []];

        // Iterate over the edited products and build the payload
        foreach ($editedProducts as $productVersion) {

            // Decode the tiers JSON. Set to empty array if null or invalid JSON.
            $tiers = json_decode($productVersion->tiers, true) ?? [];

            // Initialize the formatted tiers array
            $formattedTiers = [];
            $counter = 1;

            foreach ($tiers as $customerGroup => $tiersArray) {
                if (!is_array($tiersArray)) {
                    Log::warning("Tiers for customer group '{$customerGroup}' are not in expected format.");
                    continue;
                }

                foreach ($tiersArray as $tier) {
                    if (!isset($tier['tier_name'], $tier['price_type'], $tier['value'], $tier['type'])) {
                        Log::warning("Incomplete tier data found for customer group '{$customerGroup}'. Skipping tier.");
                        continue;
                    }

                    // Here we extract the customer group code from the tier JSON structure
                    // $customerGroupCode = $customerGroup; // Assuming customerGroup key is the code
                    $customerGroupModel = CustomerGroup::where('code', $customerGroup)->first();

                    // Format the tier data depending on 'percentage' or 'price' type
                    if ($tier['type'] === 'percentage') {
                        $formattedTier = [
                            'price_type' => $tier['price_type'],
                            'percentage_value' => $tier['value'],
                            'price' => null,
                        ];
                        if ($tier['price_type'] !== 'fixed') {
                            $formattedTier['qty_from'] = $tier['min_quantity'];
                            $formattedTier['qty_to'] = $tier['max_quantity'];
                        }
                    } elseif ($tier['type'] === 'price') {
                        $formattedTier = [
                            'price_type' => $tier['price_type'],
                            'percentage_value' => null,
                            'price' => $tier['value'],
                        ];
                        if ($tier['price_type'] !== 'fixed') {
                            $formattedTier['qty_from'] = $tier['min_quantity'];
                            $formattedTier['qty_to'] = $tier['max_quantity'];
                        }
                    }
                    //$formattedTier['customer_group_code'] = $customerGroupModel->code;
                    $formattedTier['customer_group_id'] = $customerGroupModel->group_id;
                    $formattedTier['qty'] = $counter++;

                    // Add to formatted tiers
                    $formattedTiers[] = $formattedTier;
                }
            }

            // Add each product to the 'products' array in the payload
            $payload['products'][] = [
                'sku' => $productVersion->sku,
                'price' => $productVersion->price,
                'qty' => $productVersion->stock,
                // 'product_id' => $productVersion->product_id,
                // 'item_code' => $productVersion->item_code,
                'tierPrices' => $formattedTiers,
                // 'created_at' => $productVersion->created_at,
            ];
        }

        // Optionally, you can log the payload for debugging in a secure manner
        // Log::info('Payload prepared for API:', $payload);
        //dd($payload);

        $productsEndpoint = Configuration::getValueByKey('products_endpoint');
        $productsToken = Configuration::getValueByKey('products_token');
        $baseUrl = Configuration::getValueByKey('base_url');
        $fullUrl = $baseUrl . $productsEndpoint;

        try {
            // Create a new Guzzle client
            $client = new Client();

            // Send the payload to the external API using a POST request
            $response = $client->post($fullUrl, [
                'json' => $payload, // The payload to send
                'headers' => [
                    'Authorization' => 'Bearer ' . $productsToken, // Consider using environment variables
                    'Accept' => 'application/json',
                ],
                'verify' => false, // Optional: Disable SSL verification if needed
            ]);
            $responseBody = json_decode($response->getBody(), true);
            // Check if the response status code is 200 (success)
            if ($response->getStatusCode() === 200) {
                // Clear the product_versions table after a successful API response
                ProductVersion::truncate();

                // Log the success status
                $this->logCronJob(count($payload['products']), 'Success');
                Log::info("Sent " . count($payload['products']) . " products at " . Carbon::now());

                // Return a JSON response indicating success
                return response()->json(['message' => 'Products sent successfully' . $responseBody], 200);
            } else {
                // Log an error if the API responded with a non-200 status code
                $this->logCronJob(count($payload['products']), 'Failure');
                Log::error("API responded with status: " . $response->getStatusCode());

                // Return a JSON response indicating failure
                return response()->json(['message' => 'Failed to send products'], 500);
            }

        } catch (\Exception $e) {
            // Log an error if the API call failed
            $this->logCronJob(count($payload['products']), 'Failure');
            Log::error("API call failed: " . $e->getMessage());

            // Return a JSON response indicating the API call failure
            return response()->json(['message' => 'API call failed' . $e->getMessage()], 500);
        }
    }

    protected function logCronJob($count, $status)
    {
        CronLog::create([
            'executed_at' => Carbon::now(),
            'edited_products_count' => $count,
            'status' => $status,
        ]);

    }
    public function processCronJob()
    {

        $this->sendProductDataToApi();
    }

}
