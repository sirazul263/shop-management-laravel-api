<?php

namespace App\Http\Controllers;

use App\ActivityTrait;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    use ActivityTrait;

    public function getCategories($storeId)
    {
        try {
            $categories = Category::with('user')->where('store_id', $storeId)->orderBy('created_at', 'desc')->get();

            return response()->json([
                'status' => 1,
                'message' => 'Categories retrieved successfully',
                'data' => $categories],
                200);
        } catch (Exception $e) {
            Log::error('Error on get categories'.$e->getMessage().' on line '.$e->getLine().' in file '.$e->getFile());

            return response()->json([
                'status' => 0,
                'message' => 'Failed to retrieve categories'],
                500);
        }
    }

    public function createCategory($storeId, CategoryRequest $request)
    {
        try {
            $user = $request->user();
            $category = Category::create([
                'store_id' => $storeId,
                'name' => Str::ucfirst($request->name),
                'slug' => Str::slug($request->name),
                'image' => $request->image,
                'is_active' => $request->is_active,
                'created_by' => $user->id,
            ]);

            return response()->json([
                'status' => 1,
                'message' => 'Category created successfully',
                'data' => $category],
                200);
        } catch (Exception $e) {
            Log::error('Error on create category'.$e->getMessage().' on line '.$e->getLine().' in file '.$e->getFile());

            return response()->json([
                'status' => 0,
                'message' => 'Failed to create category'],
                500);
        }
    }

    public function updateCategory($storeId, $categoryId, CategoryRequest $request)
    {

        try {
            $user = $request->user();
            $category = Category::find($categoryId)->where('store_id', $storeId)->first();
            if (! $category) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Category not found'],
                    422);
            }
            $category->update([
                'name' => Str::ucfirst($request->name),
                'slug' => Str::slug($request->name),
                'image' => $request->image,
                'is_active' => $request->is_active,
            ]);

            // Save the logs
            $this->saveRemarks($storeId, $user->id, 'Category', 'Updated', "Category updated, Category: $category->name");

            return response()->json([
                'status' => 1,
                'message' => 'Category updated successfully',
                'data' => $category,
            ],
                200);

        } catch (Exception $e) {
            Log::error('Error on update category'.$e->getMessage().' on line '.$e->getLine().' in file '.$e->getFile());

            return response()->json([
                'status' => 0,
                'message' => 'Failed to delete category'],
                500);
        }
    }

    public function deleteCategory($storeId, $categoryId, Request $request)
    {
        try {
            $user = $request->user();
            $category = Category::find($categoryId)->where('store_id', $storeId)->first();
            if (! $category) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Category not found'],
                    404);
            }
            $category->delete();
            // Save the logs
            $this->saveRemarks($storeId, $user->id, 'Category', 'Deleted', "Category deleted, Category: $category->name");

            return response()->json([
                'status' => 1,
                'message' => 'Category deleted successfully'],
                200);
        } catch (Exception $e) {
            Log::error('Error on delete category'.$e->getMessage().' on line '.$e->getLine().' in file '.$e->getFile());

            return response()->json([
                'status' => 0,
                'message' => 'Failed to delete category'],
                500);
        }
    }
}
