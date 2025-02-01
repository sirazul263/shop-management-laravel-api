<?php

namespace App\Http\Controllers;

use App\ActivityTrait;
use App\Http\Requests\BrandRequest;
use App\Models\Brand;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    use ActivityTrait;

    public function getBrands($storeId)
    {
        try {
            $brands = Brand::with('user')->where('store_id', $storeId)->orderBy('created_at', 'desc')->get();

            return response()->json([
                'status' => 1,
                'message' => 'Brands retrieved successfully',
                'data' => $brands],
                200);
        } catch (Exception $e) {
            Log::error('Error on get brands'.$e->getMessage().' on line '.$e->getLine().' in file '.$e->getFile());

            return response()->json([
                'status' => 0,
                'message' => 'Failed to retrieve brands'],
                500);
        }
    }

    public function createBrand($storeId, BrandRequest $request)
    {
        try {
            $user = $request->user();
            $brand = Brand::create([
                'store_id' => $storeId,
                'name' => Str::ucfirst($request->name),
                'slug' => Str::slug($request->name),
                'image' => $request->image,
                'is_active' => $request->is_active,
                'created_by' => $user->id,
            ]);

            return response()->json([
                'status' => 1,
                'message' => 'Brand created successfully',
                'data' => $brand],
                200);
        } catch (Exception $e) {
            Log::error('Error on create brand'.$e->getMessage().' on line '.$e->getLine().' in file '.$e->getFile());

            return response()->json([
                'status' => 0,
                'message' => 'Failed to create brand'],
                500);
        }
    }

    public function updateBrand($storeId, $brandId, BrandRequest $request)
    {

        try {
            $user = $request->user();
            $brand = Brand::find($brandId)->where('store_id', $storeId)->first();
            if (! $brand) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Brand not found'],
                    422);
            }
            $brand->update([
                'name' => Str::ucfirst($request->name),
                'slug' => Str::slug($request->name),
                'image' => $request->image,
                'is_active' => $request->is_active,
            ]);
            // Save the logs
            $this->saveRemarks($storeId, $user->id, 'Brand', 'Updated', "Brand updated, Brand: $brand->name");

            return response()->json([
                'status' => 1,
                'message' => 'Brand updated successfully',
                'data' => $brand,
            ],
                200);

        } catch (Exception $e) {
            Log::error('Error on update brand'.$e->getMessage().' on line '.$e->getLine().' in file '.$e->getFile());

            return response()->json([
                'status' => 0,
                'message' => 'Failed to delete brand'],
                500);
        }
    }

    public function deleteBrand($storeId, $brandId, Request $request)
    {
        try {
            $user = $request->user();
            $brand = Brand::find($brandId)->where('store_id', $storeId)->first();
            if (! $brand) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Brand not found'],
                    404);
            }
            $brand->delete();
            // Save the logs
            $this->saveRemarks($storeId, $user->id, 'Brand', 'Deleted', "Brand deleted, Brand: $brand->name");

            return response()->json([
                'status' => 1,
                'message' => 'Brand deleted successfully'],
                200);
        } catch (Exception $e) {
            Log::error('Error on delete brand'.$e->getMessage().' on line '.$e->getLine().' in file '.$e->getFile());

            return response()->json([
                'status' => 0,
                'message' => 'Failed to delete brand'],
                500);
        }
    }
}
