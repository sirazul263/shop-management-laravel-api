<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sell;
use App\Models\Supplier;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function getDashboardData($storeId)
    {
        try {

            // Get the current month and year
            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;

            $brands = Brand::count();
            $categories = Category::count();
            $products = Product::count();
            $suppliers = Supplier::count();

            // Query to get the total amount for today
            $totalPurchaseToday = Purchase::where('store_id', $storeId)->whereDate('created_at', Carbon::today())
                ->count();
            // Query to get the total amount for today
            $totalPurchaseAmountToday = Purchase::where('store_id', $storeId)->whereDate('created_at', Carbon::today())
                ->sum('total');
            // Query the purchases table
            $totalPurchasesThisMonth = Purchase::where('store_id', $storeId)->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->count();
            // Query to get the total amount
            $totalPurchaseAmountThisMonth = Purchase::where('store_id', $storeId)->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->sum('total');

            // Query to get the total amount for today
            $totalSalesToday = Sell::where('store_id', $storeId)->whereDate('created_at', Carbon::today())
                ->count();
            // Query to get the total amount for today
            $totalSalesAmountToday = Sell::where('store_id', $storeId)->whereDate('created_at', Carbon::today())
                ->sum('total');
            // Query the purchases table
            $totalSalesThisMonth = Sell::where('store_id', $storeId)->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->count();
            // Query to get the total amount
            $totalSalesAmountThisMonth = Sell::where('store_id', $storeId)->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->sum('total');

            return response()->json([
                'status' => 1,
                'message' => 'Dashboard data retrieved successfully',
                'data' => [
                    'brands' => $brands,
                    'categories' => $categories,
                    'products' => $products,
                    'suppliers' => $suppliers,
                    'totalPurchaseToday' => $totalPurchaseToday,
                    'totalPurchaseAmountToday' => $totalPurchaseAmountToday,
                    'totalPurchasesThisMonth' => $totalPurchasesThisMonth,
                    'totalPurchaseAmountThisMonth' => $totalPurchaseAmountThisMonth,
                    'totalSalesToday' => $totalSalesToday,
                    'totalSalesAmountToday' => $totalSalesAmountToday,
                    'totalSalesThisMonth' => $totalSalesThisMonth,
                    'totalSalesAmountThisMonth' => $totalSalesAmountThisMonth,
                ],
            ],
                200);

        } catch (Exception $e) {
            Log::error('Error on get dashboard data'.$e->getMessage().' on line '.$e->getLine().' in file '.$e->getFile());

            return response()->json([
                'status' => 0,
                'message' => 'Failed to retrieve dashboard data'],
                500);
        }
    }
}
