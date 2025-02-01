<?php

namespace App;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Log;

trait ActivityTrait
{
    public function saveRemarks(string $storeId, string $user, string $type, string $operation, string $data)
    {
        try {
            $activity = ActivityLog::create([
                'store_id' => $storeId,
                'user_id' => $user,
                'type' => $type,
                'operation' => $operation,
                'remark' => $data,
            ]);

            return $activity;
        } catch (\Exception $e) {
            Log::critical('Remarks save error');
        }

    }
}
