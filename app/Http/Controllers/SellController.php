<?php

namespace App\Http\Controllers;

use App\Http\Requests\SellRequest;
use App\Models\Product;
use App\Models\Sell;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SellController extends Controller
{
    public function getAllSells($storeId, Request $request)
    {
        try {
            $sells = Sell::with([
                'user',
                'items.product.brand',
                'items.product.category',
            ])->where('store_id', $storeId)
                ->when($request->invoiceId, function ($query, $invoiceId) {
                    return $query->where('invoice_id', $invoiceId);
                })
                ->when($request->soldBy, function ($query, $soldBy) {
                    return $query->where('user_id', $soldBy); // Assuming the column is 'user_id'
                })
                ->when($request->customer, function ($query, $customer) {
                    return $query->where('name', 'like', '%'.$customer.'%');
                })
                ->when($request->paymentStatus, function ($query, $paymentStatus) {
                    return $query->where('payment_status', $paymentStatus); // Assuming the column is 'user_id'
                })
                ->when($request->date, function ($query, $date) {
                    $formattedDate = Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');

                    return $query->whereDate('created_at', $formattedDate);
                })
                ->orderBy('created_at', 'desc')
                ->paginate(10, ['*'], 'page', $request->page);

            return response()->json([
                'status' => 1,
                'message' => 'Sells retrieved successfully',
                'data' => $sells],
                200);
        } catch (Exception $e) {
            Log::error('Error on get sells'.$e->getMessage().' on line '.$e->getLine().' in file '.$e->getFile());

            return response()->json([
                'status' => 0,
                'message' => 'Failed to retrieve sells'],
                500);
        }
    }

    public function createSell($storeId, SellRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = $user = $request->user();

            // Update Product Information
            foreach ($request->products as $product) {
                $item = Product::where('store_id', $storeId)->where('id', $product['id'])->first();
                if ($item) { // Ensure the product exists
                    if ($item->quantity < $product['quantity']) {
                        throw new \Exception("Not enough quantity of product with ID {$product['id']}. Only {$item->quantity} available.");
                    }
                    $item->quantity = $item->quantity - $product['quantity'];
                    $item->save();
                    $sellItems[] = [
                        'product_id' => $item->id,
                        'quantity' => $product['quantity'],
                        'unit_amount' => $product['unit_amount'],
                        'total_amount' => $product['unit_amount'] * $product['quantity'],
                    ];
                } else {
                    throw new \Exception("Product with ID {$product['id']} not found.");
                }
            }
            $sell = new Sell;
            $sell->store_id = $storeId;
            $sell->invoice_id = 'INV'.time();
            $sell->user_id = $user->id;
            $sell->total = $this->calculateTotalPrice($request->products, $request->discount_type, $request->discount_amount);
            $sell->total_paid = $request->total_paid;
            $sell->name = $request->name;
            $sell->phone = $request->phone;
            $sell->address = $request->address;
            $sell->payment_method = $request->payment_method;
            $sell->payment_status = $request->payment_status;
            $sell->discount_type = $request->discount_type;
            $sell->discount_amount = $request->discount_amount;
            $sell->notes = $request->notes;
            $sell->save();
            $sell->items()->createMany($sellItems);
            DB::commit();

            return response()->json([
                'status' => 1,
                'message' => 'Items added to sales successfully',
                'data' => $sell],
                200);

        } catch (Exception $e) {
            DB::rollback();
            Log::error('Error on add  items to sale'.$e->getMessage().' on line '.$e->getLine().' in file '.$e->getFile());

            return response()->json([
                'status' => 0,
                'message' => 'Failed to create add items to sale', ],
                500);

        }
    }

    public function calculateTotalPrice($products, $discount_type, $discount_amount)
    {
        $total_price = 0;
        $total_discount_amount = 0;
        foreach ($products as $product) {
            $total_price += $product['quantity'] * $product['unit_amount'];

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
