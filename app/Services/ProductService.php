<?php

namespace App\Services;

use App\Models\CustomerGroup;
use App\Models\Product;
use App\Models\ProductVersion;
use App\Models\Tier;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
                    $productId = $existingProduct->id;
                } else {
                    $newProduct = Product::create([
                        'sku' => $row[1],
                        'item_code' => $row[0],
                        'price' => $row[2],
                        'stock' => $row[3],
                    ]);
                    $productId = $newProduct->id;

                }
                $tiers = $existingProduct->tiers ?? [];

                $existingProductVersion = ProductVersion::where('product_id', $productId)->first();

                if ($existingProductVersion) {
                    // Update the existing ProductVersion record
                    $existingProductVersion->update([
                        'sku' => $row[1],
                        'price' => $row[2],
                        'stock' => $row[3],
                        'item_code' => $row[0],
                        'tiers' => $tiers,
                    ]);
                } else {
                    ProductVersion::create([
                        'product_id' => $productId,
                        'sku' => $row[1],
                        'price' => $row[2],
                        'stock' => $row[3],
                        'item_code' => $row[0],
                        'tiers' => $tiers,
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
        $this->storeProductVersion($request, $product); // store updated product to the db table
        return $product;
    }
    protected function storeProductVersion($request, $product)
    {
        // Get the product data from the request
        $productData = $request->all();
        $tiersData = $productData['tiers'] ?? []; // Get the tiers data from the request

        // Check if the product already exists in the product_versions table
        $existingVersion = ProductVersion::where('product_id', $product->id)->first();

        if ($existingVersion) {
            // Update the existing product version
            $existingVersion->update([
                'sku' => $productData['sku'],
                'item_code' => $productData['item_code'],
                'price' => $productData['price'],
                'stock' => $productData['stock'],
                'tiers' => json_encode($this->processTiers($tiersData, $product->id)),
            ]);
        } else {
            // Create a new product version without the tiers first
            $newVersion = ProductVersion::create([
                'product_id' => $product->id,
                'sku' => $productData['sku'],
                'item_code' => $productData['item_code'],
                'price' => $productData['price'],
                'stock' => $productData['stock'],
                'tiers' => json_encode($this->processTiers($tiersData, $product->id)), // Store tiers after processing
            ]);
        }
    }

/**
 * Process and save tiers from the request data.
 * This method will either create new tiers or update existing ones based on the ID.
 */
    protected function processTiers($tiersData, $productId)
    {
        $processedTiers = [];

        foreach ($tiersData as $customerGroup => $tierArray) {
            foreach ($tierArray as $tierData) {
                // Check if the tier already exists (based on the ID from the request)
                $tier = Tier::find($tierData['id']) ?? new Tier();
                if (isset($tierData['id']) && $tierData['id']) {
                    $tier = Tier::find($tierData['id']);
                    if ($tier) {
                        // If price_type is not in the request, use the existing one from the database
                        if (!isset($tierData['price_type'])) {
                            $tierData['price_type'] = $tier->price_type;

                        }
                    }
                }

                // Assign values from the request data
                $tier->product_id = $productId;
                $tier->customer_group = $customerGroup;
                $tier->tier_name = $tierData['tier_name'];
                $tier->min_quantity = $tierData['min_quantity'];
                $tier->max_quantity = $tierData['max_quantity'];
                $tier->value = $tierData['value'];
                $tier->type = $tierData['type'];
                $tier->price_type = $tierData['price_type'];

                // Save the tier (create new or update existing)
                $tier->save();

                // Add the saved tier's data (with the ID) to the processed tiers array
                $processedTiers[$customerGroup][] = [
                    'id' => $tier->id,
                    'price_type' => $tier->price_type,
                    'tier_name' => $tier->tier_name,
                    'min_quantity' => $tier->min_quantity,
                    'max_quantity' => $tier->max_quantity,
                    'value' => $tier->value,
                    'type' => $tier->type,
                ];
            }
        }

        return $processedTiers;
    }

    public function getAllProductsWithTiers()
    {
        return Product::with('tiers')->get();
    }

    public function uploadTiers($data)
    {
        DB::beginTransaction();
        try {
            foreach ($data as $index => $row) {
                if ($index === 0) {
                    continue; // Skip header row
                }

                // Ensure SKU and tier-related fields are set
                if (!isset($row[0], $row[1], $row[4], $row[5], $row[6], $row[7])) {
                    throw new \Exception("Invalid CSV format at row " . ($index + 1));
                }

                // Find product by SKU
                $existingProduct = Product::where('sku', $row[0])->first();
                if (!$existingProduct) {
                    throw new \Exception('Product with SKU ' . $row[0] . ' not found at row ' . ($index + 1));
                }

                $productId = $existingProduct->id;

                $existingTier = Tier::where('product_id', $productId)
                    ->where('tier_name', $row[1])
                    ->where('customer_group', $row[6])
                    ->first();
                if ($existingTier) {
                    // Update the existing tier
                    throw new \Exception('this tier with name' . $row[1] . 'already exist in product with SKU: ' . $row[0]);
                }

                // Prepare tier data
                $tierData = [
                    'tier_name' => $row[1],
                    'min_quantity' => $row[2],
                    'max_quantity' => $row[3],
                    'value' => $row[4],
                    'price_type' => $row[5],
                    'customer_group' => $row[6],
                    'type' => $row[7],
                ];

                // Validate tier data
                $validatedTierData = $this->validateTier($tierData, $productId, $existingProduct->tiers);
                // Create a new tier
                $existingProduct->tiers()->create($validatedTierData);

                // Refresh the product to get the updated tiers
                $existingProduct->refresh();

                // Update or create ProductVersion
                $existingProductVersion = ProductVersion::where('product_id', $productId)->first();
                $versionData = [
                    'sku' => $existingProduct->sku,
                    'price' => $existingProduct->price,
                    'stock' => $existingProduct->stock,
                    'item_code' => $existingProduct->item_code,
                    'tiers' => $existingProduct->tiers,
                ];

                if ($existingProductVersion) {
                    $existingProductVersion->update($versionData);
                } else {
                    ProductVersion::create(array_merge(['product_id' => $productId], $versionData));
                }
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('Error uploading tiers: ' . $e->getMessage());
        }
    }

    public function validateTier(array $tierData, $productId, $existingTiers)
    {
        // Ensure 'price_type' is present and handle it case-insensitively
        if (!isset($tierData['price_type'])) {
            throw new \Exception('Price type is required.');
        }

        $tierData['price_type'] = strtolower($tierData['price_type']); // Convert to lowercase for storage

        // Validate price_type
        if (!in_array($tierData['price_type'], ['fixed', 'range'])) {
            throw new \Exception('Invalid price type. Must be "fixed" or "range".');
        }

        // Ensure 'type' is present and handle it case-insensitively
        if (!isset($tierData['type'])) {
            throw new \Exception('Type is required.');
        }

        $tierData['type'] = strtolower($tierData['type']); // Convert to lowercase for storage

        // Validate type
        if (!in_array($tierData['type'], ['price', 'percentage'])) {
            throw new \Exception('Invalid type. Must be "price" or "percentage".');
        }

        // Get customer groups (assuming $customerGroups is an array)
        $customerGroups = array_map('strtoupper', CustomerGroup::pluck('code')->toArray()); // Convert all to lowercase

        //  $tierData['customer_group'] = strtolower($tierData['customer_group']); // Convert to lowercase for storage
        // Ensure 'customer_group' is present
        if (!isset($tierData['customer_group'])) {
            throw new \Exception('Customer group is required.');
        }

        // Convert customer_group to lowercase for storage
        $tierData['customer_group'] = strtoupper($tierData['customer_group']);

        // Check if customer_group exists in the allowed groups
        if (!in_array($tierData['customer_group'], $customerGroups)) {
            throw new \Exception('Invalid customer group.');
        }

        // Handle 'fixed' price type
        if ($tierData['price_type'] === 'fixed') {
            if (isset($tierData['min_quantity']) || isset($tierData['max_quantity'])) {
                throw new \Exception('Fixed tier must not contain min and max quantity.');
            }
            $tierData['min_quantity'] = null;
            $tierData['max_quantity'] = null;

            // Check if there's already a fixed tier for this product and customer group
            $existingFixedTier = Tier::where('product_id', $productId)
                ->where('customer_group', $tierData['customer_group'])
                ->where('price_type', 'fixed')
                ->first();

            if ($existingFixedTier && (!isset($tierData['id']) || $tierData['id'] != $existingFixedTier->id)) {
                throw new \Exception('Only one fixed tier is allowed per customer group for each product.');
            }
        }
        // Handle 'range' price type
        elseif ($tierData['price_type'] === 'range') {
            if (!isset($tierData['min_quantity']) || !isset($tierData['max_quantity']) || $tierData['max_quantity'] <= $tierData['min_quantity']) {
                throw new \Exception('Invalid range quantities. Max quantity must be greater than Min quantity.');
            }

            // Check for overlapping quantities
            $overlappingTier = Tier::where('product_id', $productId)
                ->where('customer_group', $tierData['customer_group'])
                ->where('price_type', 'range')
                ->where(function ($query) use ($tierData) {
                    $query->whereBetween('min_quantity', [$tierData['min_quantity'], $tierData['max_quantity']])
                        ->orWhereBetween('max_quantity', [$tierData['min_quantity'], $tierData['max_quantity']])
                        ->orWhere(function ($q) use ($tierData) {
                            $q->where('min_quantity', '<=', $tierData['min_quantity'])
                                ->where('max_quantity', '>=', $tierData['max_quantity']);
                        });
                })
                ->where('id', '!=', $tierData['id'] ?? null)
                ->first();

            if ($overlappingTier) {
                throw new \Exception('Overlapping quantities detected with existing tiers.');
            }

            // Ensure continuity of ranges
            if ($existingTiers) {
                $previousTier = $existingTiers->where('customer_group', $tierData['customer_group'])
                    ->where('price_type', 'range')
                    ->where('max_quantity', '<', $tierData['min_quantity'])
                    ->sortByDesc('max_quantity')
                    ->first();

                if ($previousTier && $previousTier->max_quantity + 1 != $tierData['min_quantity']) {
                    throw new \Exception('Tier ranges must be continuous. Expected min_quantity: ' . ($previousTier->max_quantity + 1));
                }
            }
        }

        // Ensure 'value' is greater than zero
        if (!isset($tierData['value']) || $tierData['value'] <= 0) {
            throw new \Exception('Invalid value. Value must be greater than zero.');
        }

        return $tierData;
    }

}
