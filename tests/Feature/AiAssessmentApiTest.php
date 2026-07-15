<?php

use App\Models\User;
use App\Models\Rubric;
use App\Models\Task;
use App\Models\TaskAssignment;
use App\Models\Submission;

beforeEach(function () {
    $this->mentor = User::factory()->create(['role' => 'mentor']);
    $this->researcher = User::factory()->create(['role' => 'researcher']);
});

test('mentor can create a rubric', function () {
    $response = $this->actingAs($this->mentor, 'api')->postJson('/api/mentor/rubrics', [
        'title' => 'Test Rubric',
        'description' => 'A test rubric',
        'criteria' => [
            ['name' => 'Grammar', 'points' => 10],
            ['name' => 'Content', 'points' => 20]
        ]
    ]);

    $response->assertStatus(201);
    $this->assertDatabaseHas('rubrics', [
        'title' => 'Test Rubric',
        'mentor_id' => $this->mentor->id,
        'is_global' => false
    ]);
});

test('researcher can submit a task', function () {
    $task = Task::create([
        'title' => 'Test Task',
        'description' => 'Desc',
        'mentor_id' => $this->mentor->id,
        'deadline' => now()->addDays(7)
    ]);

    $assignment = TaskAssignment::create([
        'task_id' => $task->id,
        'researcher_id' => $this->researcher->id,
        'status' => 'pending'
    ]);

    $response = $this->actingAs($this->researcher, 'api')->postJson('/api/researcher/submissions', [
        'task_assignment_id' => $assignment->id,
        'content_text' => 'This is my submission content'
    ]);

    $response->assertStatus(201);
    $this->assertDatabaseHas('submissions', [
        'task_assignment_id' => $assignment->id,
        'content_text' => 'This is my submission content'
    ]);
});

test('n8n can save feedback', function () {
    $task = Task::create([
        'title' => 'Test Task',
        'description' => 'Desc',
        'mentor_id' => $this->mentor->id,
        'deadline' => now()->addDays(7)
    ]);

    $assignment = TaskAssignment::create([
        'task_id' => $task->id,
        'researcher_id' => $this->researcher->id,
        'status' => 'submitted'
    ]);

    $submission = Submission::create([
        'task_assignment_id' => $assignment->id,
        'content_text' => 'Hello',
        'submitted_at' => now()
    ]);

    $response = $this->postJson('/api/n8n/ai-feedback', [
        'submission_id' => $submission->id,
        'overall_score' => 85,
        'rubric_scores' => [['name' => 'Grammar', 'score' => 8, 'max' => 10]],
        'general_feedback' => 'Good job'
    ]);

    $response->assertStatus(201);
    $this->assertDatabaseHas('ai_feedback', [
        'submission_id' => $submission->id,
        'overall_score' => 85,
        'status' => 'pending'
    ]);
});
