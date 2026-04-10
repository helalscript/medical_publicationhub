<?php

namespace Database\Seeders;

use App\Models\Chat;
use App\Models\ChatUser;
use App\Models\Opportunity;
use App\Models\OpportunityApplication;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Seeds opportunity applications using existing opportunities + researcher users,
 * then ensures per-opportunity group chats with mentor + accepted applicants as members.
 */
class OpportunityApplicationChatSeeder extends Seeder
{
    public function run(): void
    {
        $opportunities = Opportunity::query()->with('mentor')->get();
        $researchers = User::query()->where('role', 'researcher')->get();

        if ($opportunities->isEmpty()) {
            $this->command?->warn('OpportunityApplicationChatSeeder: no opportunities — skip applications.');

            return;
        }

        if ($researchers->isEmpty()) {
            $this->command?->warn('OpportunityApplicationChatSeeder: no researchers — skip applications.');

            return;
        }

        $maxPairs = min(60, $opportunities->count() * max(1, $researchers->count()));
        $usedKeys = [];
        $created = 0;
        $attempts = 0;
        $maxAttempts = $maxPairs * 8;

        while ($created < $maxPairs && $attempts < $maxAttempts) {
            $attempts++;
            $opp = $opportunities->random();
            $user = $researchers->random();
            $key = $opp->id.':'.$user->id;
            if (isset($usedKeys[$key])) {
                continue;
            }
            $usedKeys[$key] = true;

            $nameParts = preg_split('/\s+/', (string) $user->name, 2);
            $firstName = $nameParts[0] ?: 'Researcher';
            $lastName = $nameParts[1] ?? null;

            OpportunityApplication::factory()->create([
                'user_id' => $user->id,
                'opportunity_id' => $opp->id,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $user->email,
                'status' => fake()->randomElement(['pending', 'pending', 'pending', 'accept', 'reject']),
            ]);
            $created++;
        }

        foreach ($opportunities as $opportunity) {
            $hasApplications = OpportunityApplication::query()
                ->where('opportunity_id', $opportunity->id)
                ->exists();

            if (! $hasApplications) {
                continue;
            }

            $chat = Chat::query()->firstOrCreate(
                [
                    'chatable_id' => $opportunity->id,
                    'chatable_type' => Opportunity::class,
                ],
                [
                    'type' => 'group',
                    'status' => 'active',
                ],
            );

            $mentorUserId = $opportunity->mentor?->user_id;
            if ($mentorUserId) {
                ChatUser::query()->firstOrCreate(
                    [
                        'chat_id' => $chat->id,
                        'user_id' => (int) $mentorUserId,
                    ],
                    [
                        'role' => 'mentor',
                        'status' => 'active',
                    ],
                );
            }

            $acceptedUserIds = OpportunityApplication::query()
                ->where('opportunity_id', $opportunity->id)
                ->where('status', 'accept')
                ->pluck('user_id')
                ->unique()
                ->all();

            foreach ($acceptedUserIds as $userId) {
                ChatUser::query()->firstOrCreate(
                    [
                        'chat_id' => $chat->id,
                        'user_id' => (int) $userId,
                    ],
                    [
                        'role' => 'researcher',
                        'status' => 'active',
                    ],
                );
            }
        }
    }
}
