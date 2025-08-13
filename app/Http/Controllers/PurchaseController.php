<?php

namespace App\Http\Controllers;

use App\ActivityTrait;
use App\Http\Requests\PurchaseRequest;
use App\Models\Imei;
use App\Models\Product;
use App\Models\Purchase;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PurchaseController extends Controller
{
    use ActivityTrait;

    public function getAllPurchases($storeId, Request $request)
    {
        try {
            $purchases = Purchase::with([
                'user',
                'supplier',
                'items.product.brand',
                'items.product.category',
            ])->where('store_id', $storeId)
                ->when($request->supplier, function ($query, $supplier) {
                    return $query->where('supplier_id', $supplier);
                })
                ->when($request->purchasedBy, function ($query, $purchasedBy) {
                    return $query->where('user_id', $purchasedBy); // Assuming the column is 'user_id'
                })
                ->when($request->paymentMethod, function ($query, $paymentMethod) {
                    return $query->where('payment_method', $paymentMethod); // Assuming the column is 'user_id'
                })
                ->when($request->paymentStatus, function ($query, $paymentStatus) {
                    return $query->where('payment_status', $paymentStatus); // Assuming the column is 'user_id'
                })
                ->when($request->purchasedDate, function ($query, $purchasedDate) {
                    $formattedDate = Carbon::createFromFormat('d/m/Y', $purchasedDate)->format('Y-m-d');

                    return $query->whereDate('purchase_date', $formattedDate);
                })
                ->orderBy('created_at', 'desc')
                ->paginate(10, ['*'], 'page', $request->page);

            return response()->json([
                'status' => 1,
                'message' => 'Purchases retrieved successfully',
                'data' => $purchases],
                200);
        } catch (Exception $e) {
            Log::error('Error on get purchases'.$e->getMessage().' on line '.$e->getLine().' in file '.$e->getFile());

            return response()->json([
                'status' => 0,
                'message' => 'Failed to retrieve purchases'],
                500);
        }
    }

    public function addPurchase($storeId, PurchaseRequest $request)
    {

        DB::beginTransaction();
        try {
            $user = $user = $request->user();
            $purchaseDate = \DateTime::createFromFormat('d-m-Y H:i:s', $request->purchase_date)->format('Y-m-d H:i:s');
            $purchaseItems = [];
            // Update Product Information
            foreach ($request->products as $product) {
                $item = Product::where('store_id', $storeId)->where('id', $product['id'])->first();
                if ($item) { // Ensure the product exists
                    $item->quantity += $product['quantity'];
                    $item->price = $product['price'];
                    $item->sell_price = $product['sell_price'];
                    $item->save();

                    $purchaseItems[] = [
                        'product_id' => $item->id,
                        'quantity' => $product['quantity'],
                        'unit_amount' => $product['price'],
                        'total_amount' => $product['price'] * $product['quantity'],
                        'imei' => ! empty($product['imei']) && is_array($product['imei'])
                        ? implode(',', $product['imei'])
                        : null,
                    ];

                    // Insert IMEI numbers
                    if (! empty($product['imei']) && is_array($product['imei'])) {
                        foreach ($product['imei'] as $imei) {
                            if (! empty($imei)) {
                                Imei::create([
                                    'product_id' => $item->id,
                                    'imei' => $imei,
                                ]);
                            }
                        }
                    }
                } else {
                    throw new \Exception("Product with ID {$product['id']} not found.");
                }
            }
            $purchase = new Purchase;
            $purchase->store_id = $storeId;
            $purchase->user_id = $user->id;
            $purchase->supplier_id = $request->supplier_id;
            $purchase->purchase_date = $purchaseDate;
            $purchase->total = $this->calculateTotalPrice($request->products, $request->discount_type, $request->discount_amount);
            $purchase->payment_method = $request->payment_method;
            $purchase->payment_status = $request->payment_status;
            $purchase->discount_type = $request->discount_type;
            $purchase->discount_amount = $request->discount_amount;
            $purchase->notes = $request->notes;
            $purchase->save();
            $purchase->items()->createMany($purchaseItems);
            DB::commit();

            return response()->json([
                'status' => 1,
                'message' => 'Items purchased successfully',
                'data' => $purchase],
                200);

        } catch (Exception $e) {
            DB::rollback();
            Log::error('Error on purchase items'.$e->getMessage().' on line '.$e->getLine().' in file '.$e->getFile());

            return response()->json([
                'status' => 0,
                'message' => 'Failed to create purchase items'],
                500);

        }
    }

    public function calculateTotalPrice($products, $discount_type, $discount_amount)
    {
        $total_price = 0;
        $total_discount_amount = 0;
        foreach ($products as $product) {
            $total_price += $product['quantity'] * $product['price'];

        }
        if ($discount_type && $discount_amount) {
            if ($discount_type == 'FIXED') {
                $total_discount_amount = $discount_amount;
            } else {
                $total_discount_amount = $total_price * $discount_amount / 100;
            }
        }

        return $total_price - $total_discount_amount;
    }
}
