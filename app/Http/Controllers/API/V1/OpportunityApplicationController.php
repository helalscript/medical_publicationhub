<?php

namespace App\Http\Controllers\API\V1;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Opportunity;
use App\Models\OpportunityApplication;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class OpportunityApplicationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'per_page' => 'sometimes|integer|min:1|max:100',
        ]);

        try {
            $perPage = (int) ($request->input('per_page', 15));
            $data = OpportunityApplication::with('opportunity')
                ->where('user_id', $request->user()->id)
                ->latest('id')
                ->paginate($perPage);

            return Helper::jsonResponse(true, 'Applications retrieved successfully.', 200, $data, true);
        } catch (Exception $e) {
            Log::error('API\\V1\\OpportunityApplicationController::index '.$e->getMessage());

            return Helper::jsonErrorResponse('Failed to load applications.', 500);
        }
    }

    public function show(Request $request, string $id): JsonResponse
    {
        try {
            $data = OpportunityApplication::with('opportunity')
                ->where('user_id', $request->user()->id)
                ->findOrFail($id);

            return Helper::jsonResponse(true, 'Application retrieved successfully.', 200, $data);
        } catch (Exception $e) {
            Log::error('API\\V1\\OpportunityApplicationController::show '.$e->getMessage());

            return Helper::jsonErrorResponse('Application not found.', 404);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'opportunity_id' => 'required|exists:opportunities,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'highest_edu_level' => 'nullable|string|max:255',
            'institution' => 'nullable|string|max:255',
            'field_of_study' => 'nullable|string|max:255',
            'research_experience' => 'nullable|string',
            'current_position' => 'nullable|string|max:255',
            'linkedin_profile' => 'nullable|string|max:255',
            'personal_website' => 'nullable|string|max:255',
            'cover_letter' => 'nullable|string',
            'accept_term' => 'required|boolean',
            'documents' => 'nullable|array',
            'documents.*' => 'file|mimes:pdf,jpg,jpeg,png,webp,doc,docx|max:10240',
        ]);

        try {
            if (! $request->boolean('accept_term')) {
                throw new RuntimeException('You must accept the terms.');
            }

            $op = Opportunity::with(['mentor', 'chat'])->where('status', 'active')->findOrFail($validated['opportunity_id']);
            if ($op->dead_line && now()->toDateString() > $op->dead_line->toDateString()) {
                throw new RuntimeException('Deadline has passed for this opportunity.');
            }

            $documents = [];
            foreach ($request->file('documents', []) as $file) {
                $path = Helper::fileUpload($file, 'opportunity-applications/documents', time().'_'.uniqid('', true));
                if ($path) {
                    $documents[] = [
                        'path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                    ];
                }
            }

            $application = OpportunityApplication::create([
                'user_id' => $request->user()->id,
                'opportunity_id' => $op->id,
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'] ?? null,
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'highest_edu_level' => $validated['highest_edu_level'] ?? null,
                'institution' => $validated['institution'] ?? null,
                'field_of_study' => $validated['field_of_study'] ?? null,
                'research_experience' => $validated['research_experience'] ?? null,
                'current_position' => $validated['current_position'] ?? null,
                'linkedin_profile' => $validated['linkedin_profile'] ?? null,
                'personal_website' => $validated['personal_website'] ?? null,
                'documents' => $documents ?: null,
                'cover_letter' => $validated['cover_letter'] ?? null,
                'accept_term' => (bool) $validated['accept_term'],
                'status' => 'pending',
            ]);

            // Ensure an opportunity-level chat exists (group), but DO NOT add applicant yet.
            // Applicant will be added when admin accepts the application.
            $mentorUserId = $op->mentor?->user_id;
            if (! empty($mentorUserId) && ! $op->chat) {
                $chat = \App\Models\Chat::create([
                    'chatable_id' => $op->id,
                    'chatable_type' => Opportunity::class,
                    'type' => 'group',
                    'status' => 'active',
                ]);
                \App\Models\ChatUser::firstOrCreate([
                    'chat_id' => $chat->id,
                    'user_id' => (int) $mentorUserId,
                ], [
                    'role' => 'mentor',
                    'status' => 'active',
                ]);
            }

            return Helper::jsonResponse(true, 'Application submitted successfully.', 201, $application);
        } catch (RuntimeException $e) {
            return Helper::jsonErrorResponse($e->getMessage(), 422);
        } catch (Exception $e) {
            Log::error('API\\V1\\OpportunityApplicationController::store '.$e->getMessage());

            return Helper::jsonErrorResponse('Failed to submit application.', 500);
        }
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|nullable|string|max:255',
            'email' => 'sometimes|required|email|max:255',
            'phone' => 'sometimes|nullable|string|max:50',
            'highest_edu_level' => 'sometimes|nullable|string|max:255',
            'institution' => 'sometimes|nullable|string|max:255',
            'field_of_study' => 'sometimes|nullable|string|max:255',
            'research_experience' => 'sometimes|nullable|string',
            'current_position' => 'sometimes|nullable|string|max:255',
            'linkedin_profile' => 'sometimes|nullable|string|max:255',
            'personal_website' => 'sometimes|nullable|string|max:255',
            'cover_letter' => 'sometimes|nullable|string',
            'documents' => 'sometimes|array',
            'documents.*' => 'file|mimes:pdf,jpg,jpeg,png,webp,doc,docx|max:10240',
        ]);

        try {
            $app = OpportunityApplication::where('user_id', $request->user()->id)->findOrFail($id);
            if ($app->status !== 'pending') {
                throw new RuntimeException('Only pending applications can be updated.');
            }

            if ($request->hasFile('documents')) {
                $documents = $app->documents ?? [];
                foreach ($request->file('documents', []) as $file) {
                    $path = Helper::fileUpload($file, 'opportunity-applications/documents', time().'_'.uniqid('', true));
                    if ($path) {
                        $documents[] = [
                            'path' => $path,
                            'original_name' => $file->getClientOriginalName(),
                        ];
                    }
                }
                $validated['documents'] = $documents;
            } else {
                unset($validated['documents']);
            }

            $app->update($validated);

            return Helper::jsonResponse(true, 'Application updated successfully.', 200, $app->fresh());
        } catch (RuntimeException $e) {
            return Helper::jsonErrorResponse($e->getMessage(), 422);
        } catch (Exception $e) {
            Log::error('API\\V1\\OpportunityApplicationController::update '.$e->getMessage());

            return Helper::jsonErrorResponse('Failed to update application.', 500);
        }
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        try {
            $app = OpportunityApplication::where('user_id', $request->user()->id)->findOrFail($id);
            if ($app->status !== 'pending') {
                throw new RuntimeException('Only pending applications can be deleted.');
            }
            $app->delete();

            return Helper::jsonResponse(true, 'Application deleted successfully.', 200);
        } catch (RuntimeException $e) {
            return Helper::jsonErrorResponse($e->getMessage(), 422);
        } catch (Exception $e) {
            Log::error('API\\V1\\OpportunityApplicationController::destroy '.$e->getMessage());

            return Helper::jsonErrorResponse('Failed to delete application.', 500);
        }
    }
}

