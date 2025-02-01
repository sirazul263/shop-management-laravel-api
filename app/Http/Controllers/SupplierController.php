<?php

namespace App\Http\Controllers;

use App\ActivityTrait;
use App\Http\Requests\SupplierRequest;
use App\Models\Supplier;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SupplierController extends Controller
{
    use ActivityTrait;

    public function getSuppliers($storeId)
    {
        try {
            $suppliers = Supplier::with('user')->where('store_id', $storeId)->orderBy('created_at', 'desc')->get();

            return response()->json([
                'status' => 1,
                'message' => 'Suppliers retrieved successfully',
                'data' => $suppliers],
                200);
        } catch (Exception $e) {
            Log::error('Error on get suppliers'.$e->getMessage().' on line '.$e->getLine().' in file '.$e->getFile());

            return response()->json([
                'status' => 0,
                'message' => 'Failed to retrieve suppliers'],
                500);
        }
    }

    public function createSupplier($storeId, SupplierRequest $request)
    {
        try {
            $user = $request->user();
            $supplier = Supplier::create([
                'store_id' => $storeId,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'created_by' => $user->id,
            ]);

            return response()->json([
                'status' => 1,
                'message' => 'Supplier created successfully',
                'data' => $supplier],
                200);
        } catch (Exception $e) {
            Log::error('Error on create supplier'.$e->getMessage().' on line '.$e->getLine().' in file '.$e->getFile());

            return response()->json([
                'status' => 0,
                'message' => 'Failed to create supplier'],
                500);
        }
    }

    public function updateSupplier($storeId, $supplierId, SupplierRequest $request)
    {

        try {
            $user = $request->user();
            $supplier = Supplier::find($supplierId)->where('store_id', $storeId)->first();
            if (! $supplier) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Supplier not found'],
                    422);
            }
            $supplier->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
            ]);
            // Save the logs
            $this->saveRemarks($storeId, $user->id, 'Supplier', 'Updated', "Supplier updated, Supplier: $supplier->name");

            return response()->json([
                'status' => 1,
                'message' => 'Supplier updated successfully',
                'data' => $supplier,
            ],
                200);

        } catch (Exception $e) {
            Log::error('Error on update supplier'.$e->getMessage().' on line '.$e->getLine().' in file '.$e->getFile());

            return response()->json([
                'status' => 0,
                'message' => 'Failed to delete supplier'],
                500);
        }
    }

    public function deleteSupplier($storeId, $supplierId, Request $request)
    {
        try {
            $user = $request->user();
            $supplier = Supplier::find($supplierId)->where('store_id', $storeId)->first();

            if (! $supplier) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Supplier not found'],
                    404);
            }
            $supplier->delete();
            // Save the logs
            $this->saveRemarks($storeId, $user->id, 'Supplier', 'Deleted', "Supplier deleted, Supplier: $supplier->name");

            return response()->json([
                'status' => 1,
                'message' => 'Supplier deleted successfully'],
                200);
        } catch (Exception $e) {
            Log::error('Error on delete supplier'.$e->getMessage().' on line '.$e->getLine().' in file '.$e->getFile());

            return response()->json([
                'status' => 0,
                'message' => 'Failed to delete supplier'],
                500);
        }
    }
}
