<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductFilterRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

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

}