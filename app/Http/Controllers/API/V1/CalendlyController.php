<?php

namespace App\Http\Controllers\API\V1;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Exception;
use Illuminate\Support\Facades\Log;

class CalendlyController extends Controller
{
    public function scheduleLink()
    {
        try {
            $settings = SystemSetting::query()->select('calendly_scheduling_url')->first();
            $url = $settings?->calendly_scheduling_url;

            if (empty($url)) {
                return Helper::jsonErrorResponse('Scheduling link is not configured yet.', 404);
            }

            return Helper::jsonResponse(true, 'Scheduling link retrieved successfully.', 200, [
                'url' => $url,
            ]);
        } catch (Exception $e) {
            Log::error('CalendlyController::scheduleLink '.$e->getMessage());
            return Helper::jsonErrorResponse('Failed to load scheduling link.', 500);
        }
    }
}
