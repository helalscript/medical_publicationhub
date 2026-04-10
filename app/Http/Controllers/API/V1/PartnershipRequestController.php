<?php

namespace App\Http\Controllers\API\V1;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\PartnershipRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PartnershipRequestController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'full_name' => ['required', 'string', 'max:255'],
                'organization' => ['nullable', 'string', 'max:255'],
                'email' => ['nullable', 'email', 'max:255'],
                'phone' => ['nullable', 'string', 'max:50'],
                'message' => ['nullable', 'string'],
            ]);

            $validated['ip'] = $request->ip();
            $validated['status'] = 'pending';

            $data = PartnershipRequest::create($validated);

            return Helper::jsonResponse(true, 'Request submitted successfully.', 201, $data);
        } catch (Exception $e) {
            Log::error('PartnershipRequestController::store '.$e->getMessage());
            return Helper::jsonErrorResponse('Failed to submit request.', 500);
        }
    }
}
