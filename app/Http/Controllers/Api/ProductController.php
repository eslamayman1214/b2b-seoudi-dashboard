<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductFilterRequest;
use App\Http\Requests\UpdateProductRequest;
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
                $products = $this->productService->getAllProductsWithTiers();
                return response()->json(['success' => true, 'data' => $products]);
            } else {
                return response()->json(['success' => false, 'message' => 'User not authorized.'], 403);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error fetching products with tiers: ' . $e->getMessage()], 500);
        }
    }
}
