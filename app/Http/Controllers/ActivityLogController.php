<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ActivityLogController extends Controller
{
    public function getActivityLog($storeId, Request $request)
    {
        //  dd($request->date);
        try {
            $logs = ActivityLog::with('user')->where('store_id', $storeId) // Load the 'user' relationship
                ->when($request->type, function ($query, $type) {
                    return $query->where('type', $type);
                })
                ->when($request->user, function ($query, $user) {
                    return $query->where('user_id', $user); // Assuming the column is 'user_id'
                })
                ->when($request->date, function ($query, $date) {
                    $formattedDate = Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');

                    return $query->whereDate('created_at', $formattedDate);
                })
                ->orderBy('created_at', 'desc')
                ->paginate(10, ['*'], 'page', $request->page); // Pass the page number

            return response()->json([
                'status' => 1,
                'message' => 'Logs retrieved successfully',
                'data' => $logs],
                200);
        } catch (Exception $e) {
            Log::error('Error on get logs'.$e->getMessage().' on line '.$e->getLine().' in file '.$e->getFile());

            return response()->json([
                'status' => 0,
                'message' => 'Failed to retrieve logs'],
                500);
        }
    }
}
