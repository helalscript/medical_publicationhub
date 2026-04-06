<?php

namespace App\Http\Controllers\API\V1\Mentor;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Services\ResearchIdeaService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class ResearchIdeaController extends Controller
{
    public function __construct(protected ResearchIdeaService $researchIdeaService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'per_page' => 'sometimes|integer|min:1|max:100',
        ]);

        try {
            $perPage = (int) ($request->input('per_page', 15));
            $data = $this->researchIdeaService->mentorList($request->user(), $perPage);

            return Helper::jsonResponse(true, 'Research ideas retrieved successfully.', 200, $data, true);
        } catch (Exception $e) {
            Log::error('API\V1\Mentor\ResearchIdeaController::index '.$e->getMessage());

            return Helper::jsonErrorResponse('Failed to load research ideas.', 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'study_title' => 'required|string|max:500',
            'study_descritions' => 'required|string',
            'research_objectives' => 'required|string',
            'perposed_methodology' => 'required|string',
        ]);

        try {
            $idea = $this->researchIdeaService->mentorCreate($request->user(), $validated);

            return Helper::jsonResponse(true, 'Research idea created successfully.', 201, $idea);
        } catch (RuntimeException $e) {
            return Helper::jsonErrorResponse($e->getMessage(), 422);
        } catch (Exception $e) {
            Log::error('API\V1\Mentor\ResearchIdeaController::store '.$e->getMessage());

            return Helper::jsonErrorResponse('Failed to create research idea.', 500);
        }
    }

    public function show(Request $request, string $id): JsonResponse
    {
        try {
            $idea = $this->researchIdeaService->mentorShow($request->user(), (int) $id);

            return Helper::jsonResponse(true, 'Research idea retrieved successfully.', 200, $idea);
        } catch (Exception $e) {
            Log::error('API\V1\Mentor\ResearchIdeaController::show '.$e->getMessage());

            return Helper::jsonErrorResponse('Research idea not found.', 404);
        }
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'study_title' => 'required|string|max:500',
            'study_descritions' => 'required|string',
            'research_objectives' => 'required|string',
            'perposed_methodology' => 'required|string',
        ]);

        try {
            $idea = $this->researchIdeaService->mentorUpdate($request->user(), (int) $id, $validated);

            return Helper::jsonResponse(true, 'Research idea updated successfully.', 200, $idea);
        } catch (RuntimeException $e) {
            return Helper::jsonErrorResponse($e->getMessage(), 422);
        } catch (Exception $e) {
            Log::error('API\V1\Mentor\ResearchIdeaController::update '.$e->getMessage());

            return Helper::jsonErrorResponse('Failed to update research idea.', 500);
        }
    }

    public function submitSubmissionProof(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'proof_files' => 'required|array|min:1',
            'proof_files.*' => 'file|mimes:pdf,jpg,jpeg,png,webp|max:10240',
        ]);

        try {
            $proofItems = [];
            foreach ($request->file('proof_files', []) as $file) {
                $path = Helper::fileUpload($file, 'research-ideas/submission-proof', time().'_'.uniqid('', true));
                if ($path) {
                    $proofItems[] = [
                        'path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                    ];
                }
            }

            if ($proofItems === []) {
                return Helper::jsonErrorResponse('No valid files uploaded.', 422);
            }

            $idea = $this->researchIdeaService->submitSubmissionProof($request->user(), (int) $id, $proofItems);

            return Helper::jsonResponse(true, 'Submission proof uploaded successfully.', 200, $idea);
        } catch (RuntimeException $e) {
            return Helper::jsonErrorResponse($e->getMessage(), 422);
        } catch (Exception $e) {
            Log::error('API\V1\Mentor\ResearchIdeaController::submitSubmissionProof '.$e->getMessage());

            return Helper::jsonErrorResponse('Failed to upload submission proof.', 500);
        }
    }
}
