<?php

namespace App\Http\Middleware;

use App\Models\Store;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserStore
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        $storeId = $request->route('storeId'); // Get store_id from route parameter (adjust if needed)

        // Check if the store exists
        $store = Store::find($storeId);
        if (! $store) {
            return response()->json([
                'status' => 0,
                'message' => 'Store not found',
            ], 422);
        }

        // Check if the user belongs to the store
        if (! $store->users()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'status' => 0,
                'message' => 'Unauthorized: You do not belong to this store',
            ], 403);
        }

        // If everything is fine, continue with the request
        return $next($request);
    }
}
