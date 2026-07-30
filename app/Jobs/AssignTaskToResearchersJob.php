<?php

namespace App\Jobs;

use App\Models\Task;
use App\Models\TaskAssignment;
use App\Models\OpportunityApplication;
use App\Models\User;
use App\Notifications\TaskAssignedNotification;
use App\Events\TaskAssignedEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AssignTaskToResearchersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $taskId,
        public int $opportunityId,
        public string $assignType,
        public array $researcherIds = []
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('[AssignTaskToResearchersJob] Processing task assignments', [
            'task_id' => $this->taskId,
            'opportunity_id' => $this->opportunityId,
            'assign_type' => $this->assignType,
            'researcher_ids' => $this->researcherIds,
        ]);

        $task = Task::with('opportunity')->find($this->taskId);
        if (!$task) {
            Log::error('[AssignTaskToResearchersJob] Task not found', ['task_id' => $this->taskId]);
            return;
        }

        $userIds = [];

        if ($this->assignType === 'all') {
            $userIds = OpportunityApplication::where('opportunity_id', $this->opportunityId)
                ->where('status', 'accept')
                ->pluck('user_id')
                ->toArray();
        } else {
            $userIds = $this->researcherIds;
        }

        if (empty($userIds)) {
            Log::warning('[AssignTaskToResearchersJob] No researchers to assign.', [
                'task_id' => $this->taskId,
                'opportunity_id' => $this->opportunityId,
            ]);
            return;
        }

        foreach ($userIds as $userId) {
            // Create or update task assignment record
            TaskAssignment::updateOrCreate(
                ['task_id' => $task->id, 'researcher_id' => $userId],
                ['status' => 'pending']
            );

            $user = User::find($userId);
            if ($user) {
                // Send system notification (database & broadcast)
                $user->notify(new TaskAssignedNotification($task));

                // Dispatch real-time broadcast event
                event(new TaskAssignedEvent($task, $userId));
            }
        }

        Log::info('[AssignTaskToResearchersJob] Task assignments completed successfully', [
            'task_id' => $this->taskId,
            'assigned_count' => count($userIds),
        ]);
    }
}
