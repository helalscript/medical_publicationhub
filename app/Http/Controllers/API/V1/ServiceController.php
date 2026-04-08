<?php

namespace App\Http\Controllers\API\V1;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\SystemSetting;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        try {
            $q = Service::query()->where('status', 'active');

            if ($request->filled('featured')) {
                $q->where('is_features', (bool) $request->boolean('featured'));
            }

            if ($request->filled('search')) {
                $search = (string) $request->get('search');
                $q->where('title', 'like', "%{$search}%");
            }

            $defaultWhatsapp = SystemSetting::query()->value('whatsapp_number');
            $data = $q->latest()->paginate((int) ($request->get('per_page', 10)));

            $data->getCollection()->transform(function ($s) use ($defaultWhatsapp) {
                $s->effective_whatsapp_number = $s->whats_app_num ?: $defaultWhatsapp;
                return $s;
            });

            return Helper::jsonResponse(true, 'Services retrieved successfully.', 200, $data, true);
        } catch (Exception $e) {
            Log::error('ServiceController::index '.$e->getMessage());
            return Helper::jsonErrorResponse('Failed to load services.', 500);
        }
    }

    public function show(int $id)
    {
        try {
            $data = Service::where('status', 'active')->findOrFail($id);
            $defaultWhatsapp = SystemSetting::query()->value('whatsapp_number');

            $data->effective_whatsapp_number = $data->whats_app_num ?: $defaultWhatsapp;

            return Helper::jsonResponse(true, 'Service retrieved successfully.', 200, $data);
        } catch (Exception $e) {
            Log::error('ServiceController::show '.$e->getMessage());
            return Helper::jsonErrorResponse('Service not found.', 404);
        }
    }
}
