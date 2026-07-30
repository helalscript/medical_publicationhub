<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class TaskAssignedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Task $task)
    {
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'New Task Assigned',
            'message' => 'You have been assigned a new task: ' . $this->task->title,
            'url' => '/tasks/' . $this->task->id,
            'type' => 'task_assigned',
            'thumbnail' => $this->task->opportunity?->thumbnail ?? null,
            'task_id' => $this->task->id,
        ];
    }

    /**
     * Get the broadcast representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title' => 'New Task Assigned',
            'message' => 'You have been assigned a new task: ' . $this->task->title,
            'url' => '/tasks/' . $this->task->id,
            'type' => 'task_assigned',
            'thumbnail' => $this->task->opportunity?->thumbnail ?? null,
            'task_id' => $this->task->id,
        ]);
    }
}
