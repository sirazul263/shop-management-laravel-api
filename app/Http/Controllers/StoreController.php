<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRequest;
use App\Models\Store;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class StoreController extends Controller
{
    public function getStores()
    {
        try {
            $stores = Auth::user()->stores;

            return response()->json([
                'status' => 1,
                'message' => 'Stores retrieved successfully',
                'data' => $stores
            ], 200);
        } catch (Exception $e) {
            Log::error('Error on get stores '.$e->getMessage().' on line '.$e->getLine().' in file '.$e->getFile());

            return response()->json([
                'status' => 0,
                'message' => 'Failed to retrieve stores'
            ], 500);
        }
    }

    public function createStore(StoreRequest $request)
    {
        try {
            $user = $request->user();
            $store = Store::create([
                'name' => Str::ucfirst($request->name),
                'address' => $request->address,
                'phone' => $request->phone,
                'description' => $request->description,
                'image' => $request->image,
                'status' => 'ACTIVE',
                'created_by' => $user->id,
            ]);

            return response()->json([
                'status' => 1,
                'message' => 'Store created successfully',
                'data' => $store
            ], 200);
        } catch (Exception $e) {
            Log::error('Error on create store '.$e->getMessage().' on line '.$e->getLine().' in file '.$e->getFile());

            return response()->json([
                'status' => 0,
                'message' => 'Failed to create store'
            ], 500);
        }
    }

    public function updateStore(StoreRequest $request, $storeId)
    {
        try {
            $store = Store::findOrFail($storeId);

            // Optional: Ensure the authenticated user is the creator of the store
            if ($store->created_by !== Auth::id()) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Unauthorized action'
                ], 403);
            }

            $store->update([
                'name' => Str::ucfirst($request->name),
                'address' => $request->address,
                'phone' => $request->phone,
                'description' => $request->description,
                'image' => $request->image,
                'status' => $request->status ?? $store->status,
            ]);

            return response()->json([
                'status' => 1,
                'message' => 'Store updated successfully',
                'data' => $store
            ], 200);
        } catch (Exception $e) {
            Log::error('Error on update store '.$e->getMessage().' on line '.$e->getLine().' in file '.$e->getFile());

            return response()->json([
                'status' => 0,
                'message' => 'Failed to update store'
            ], 500);
        }
    }

    public function deleteStore($id)
    {
        try {
            $store = Store::findOrFail($id);

            // Optional: Ensure the authenticated user is the creator of the store
            if ($store->created_by !== Auth::id()) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Unauthorized action'
                ], 403);
            }

            $store->delete();

            return response()->json([
                'status' => 1,
                'message' => 'Store deleted successfully'
            ], 200);
        } catch (Exception $e) {
            Log::error('Error on delete store '.$e->getMessage().' on line '.$e->getLine().' in file '.$e->getFile());

            return response()->json([
                'status' => 0,
                'message' => 'Failed to delete store'
            ], 500);
        }
    }
}
