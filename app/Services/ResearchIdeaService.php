<?php

namespace App\Services;

use App\Models\Mentor;
use App\Models\ResearchIdea;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ResearchIdeaService
{
    public function resolveMentorForUser(User $user): Mentor
    {
        $mentor = Mentor::where('user_id', $user->id)->first();
        if (! $mentor) {
            throw new RuntimeException('Mentor profile not found for this user.');
        }
        if ($user->role !== 'mentor') {
            throw new RuntimeException('User is not a mentor.');
        }

        return $mentor;
    }

    public function mentorList(User $user, int $perPage = 15): LengthAwarePaginator
    {
        $mentor = $this->resolveMentorForUser($user);

        return ResearchIdea::query()
            ->where('mentor_id', $mentor->id)
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function mentorShow(User $user, int $id): ResearchIdea
    {
        $mentor = $this->resolveMentorForUser($user);

        return ResearchIdea::query()
            ->where('mentor_id', $mentor->id)
            ->whereKey($id)
            ->firstOrFail();
    }

    public function mentorCreate(User $user, array $data): ResearchIdea
    {
        $mentor = $this->resolveMentorForUser($user);

        return DB::transaction(function () use ($user, $mentor, $data) {
            return ResearchIdea::create([
                'user_id' => $user->id,
                'mentor_id' => $mentor->id,
                'study_title' => $data['study_title'],
                'study_descritions' => $data['study_descritions'],
                'research_objectives' => $data['research_objectives'],
                'perposed_methodology' => $data['perposed_methodology'],
                'status' => 'pending',
            ]);
        });
    }

    public function mentorUpdate(User $user, int $id, array $data): ResearchIdea
    {
        $idea = $this->mentorShow($user, $id);
        $this->assertMentorCanEditProposal($idea);

        $idea->update([
            'study_title' => $data['study_title'],
            'study_descritions' => $data['study_descritions'],
            'research_objectives' => $data['research_objectives'],
            'perposed_methodology' => $data['perposed_methodology'],
        ]);

        return $idea->fresh();
    }

    public function submitSubmissionProof(User $user, int $id, array $proofItems): ResearchIdea
    {
        $idea = $this->mentorShow($user, $id);
        if ($idea->status !== 'accept') {
            throw new RuntimeException('Submission proof is only allowed when the research idea is accepted.');
        }

        $existing = $idea->submission_proof ?? [];
        $merged = array_values(array_merge($existing, $proofItems));

        $idea->update(['submission_proof' => $merged]);

        return $idea->fresh();
    }

    public function adminQuery(): Builder
    {
        return ResearchIdea::query()->with(['mentor.user', 'user', 'updatedByUser'])->latest('id');
    }

    public function adminUpdate(ResearchIdea $idea, User $admin, array $data): ResearchIdea
    {
        return DB::transaction(function () use ($idea, $admin, $data) {
            $payload = [
                'dead_line' => $data['dead_line'] ?? null,
                'start_date' => $data['start_date'] ?? null,
                'end_date' => $data['end_date'] ?? null,
                'status' => $data['status'],
                'updated_by' => $admin->id,
            ];

            $idea->update($payload);

            return $idea->fresh();
        });
    }

    /**
     * @return Collection<int, ResearchIdea>
     */
    public function adminListAll(): Collection
    {
        return ResearchIdea::query()->with(['mentor.user', 'user'])->latest('id')->get();
    }

    protected function assertMentorCanEditProposal(ResearchIdea $idea): void
    {
        if (! in_array($idea->status, ['pending', 'on_review'], true)) {
            throw new RuntimeException('This research idea can no longer be edited.');
        }
    }
}
