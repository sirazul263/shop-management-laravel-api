<?php

namespace App\Http\Controllers;

use App\ActivityTrait;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    use ActivityTrait;

    public function getAllProducts($storeId)
    {
        try {
            $products = Product::with(['user', 'brand', 'category' , 'imeis'])->where('store_id', $storeId)->orderBy('created_at', 'desc')->get();

            return response()->json([
                'status' => 1,
                'message' => 'Products retrieved successfully',
                'data' => $products],
                200);
        } catch (Exception $e) {
            Log::error('Error on get products'.$e->getMessage().' on line '.$e->getLine().' in file '.$e->getFile());

            return response()->json([
                'status' => 0,
                'message' => 'Failed to retrieve products'],
                500);
        }
    }

    public function getSingleProduct($storeId, $productId)
    {
        try {
            $product = Product::find($productId)->where('store_id', $storeId)->where('id', $productId)->first();
            if (! $product) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Product not found'],
                    422);
            }

            return response()->json([
                'status' => 1,
                'message' => 'Product retrieved successfully',
                'data' => $product],
                200);
        } catch (Exception $e) {
            Log::error('Error on get products'.$e->getMessage().' on line '.$e->getLine().' in file '.$e->getFile());

            return response()->json([
                'status' => 0,
                'message' => 'Failed to retrieve product'],
                500);
        }
    }

    public function createProduct($storeId, ProductRequest $request)
    {
        try {
            $user = $request->user();
            $product = new Product;
            $product->store_id = $storeId;
            $product->category_id = $request->category_id;
            $product->brand_id = $request->brand_id;
            $product->name = $request->name;
            $product->slug = Str::slug($request->name);
            $product->quantity = 0;
            $product->price = $request->price;
            $product->description = $request->description;
            $product->image = $request->image;
            $product->created_by = $user->id;
            $product->save();

            return response()->json([
                'status' => 1,
                'message' => 'Product created successfully',
                'data' => $product],
                200);
        } catch (Exception $e) {
            Log::error('Error on create product'.$e->getMessage().' on line '.$e->getLine().' in file '.$e->getFile());

            return response()->json([
                'status' => 0,
                'message' => 'Failed to create product'],
                500);
        }
    }

    public function updateProduct($storeId, $productId, ProductRequest $request)
    {
        try {
            $user = $request->user();
            $product = Product::find($productId)->where('store_id', $storeId)->first();
            if (! $product) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Product not found'],
                    422);
            }
            $product->category_id = $request->category_id;
            $product->brand_id = $request->brand_id;
            $product->name = $request->name;
            $product->slug = Str::slug($request->name);
            $product->price = $request->price;
            $product->description = $request->description;
            $product->image = $request->image;
            $product->save();
            // Save the logs
            $this->saveRemarks($storeId, $user->id, 'Product', 'Updated', "Product updated, Product: $product->name");

            return response()->json([
                'status' => 1,
                'message' => 'Product updated successfully',
                'data' => $product],
                200);
        } catch (Exception $e) {
            Log::error('Error on update product'.$e->getMessage().' on line '.$e->getLine().' in file '.$e->getFile());

            return response()->json([
                'status' => 0,
                'message' => 'Failed to update product'],
                500);
        }
    }

    public function deleteProduct($storeId, $productId, Request $request)
    {
        try {
            $user = $request->user();
            $product = Product::find($productId)->where('store_id', $storeId)->first();
            if (! $product) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Product not found'],
                    422);
            }
            $product->delete();
            // Save the logs
            $this->saveRemarks($storeId, $user->id, 'Product', 'Delete', "Product deleted, Product: $product->name");

            return response()->json([
                'status' => 1,
                'message' => 'Product deleted successfully',
                'data' => $product],
                200);
        } catch (Exception $e) {
            Log::error('Error on delete product'.$e->getMessage().' on line '.$e->getLine().' in file '.$e->getFile());

            return response()->json([
                'status' => 0,
                'message' => 'Failed to delete product'],
                500);
        }
    }
}
