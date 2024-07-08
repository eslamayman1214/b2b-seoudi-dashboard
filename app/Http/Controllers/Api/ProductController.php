<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductFilterRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Services\productService;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{

    public function __construct(private ProductService $productService)
    {
    }
    public function index(ProductFilterRequest $request)
    {
        $products = $this->productService->getProducts($request);
        return response()->json($products);
    }

    public function update(UpdateProductRequest $request, $id)
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
