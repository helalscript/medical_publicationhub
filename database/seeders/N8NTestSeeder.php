<?php

namespace Database\Seeders;

use App\Models\AiFeedback;
use App\Models\Rubric;
use App\Models\Submission;
use App\Models\Task;
use App\Models\TaskAssignment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class N8NTestSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚀 Starting N8N Full Test Data Seeder...');
        $this->command->newLine();

        // =============================================
        // 1. Create Mentor User
        // =============================================
        $this->command->info('👤 Creating Mentor user...');
        $mentor = User::firstOrCreate(
            ['email' => 'mentor.test@svasyndicate.com'],
            [
                'name'     => 'Dr. Ahmed Al-Farsi',
                'password' => Hash::make('password123'),
                'role'     => 'mentor',
                'email_verified_at' => now(),
            ]
        );
        $this->command->line("   ✅ Mentor: {$mentor->name} (ID: {$mentor->id})");

        // =============================================
        // 2. Create Researcher User
        // =============================================
        $this->command->info('👤 Creating Researcher user...');
        $researcher = User::firstOrCreate(
            ['email' => 'researcher.test@svasyndicate.com'],
            [
                'name'     => 'Sara Al-Mansouri',
                'password' => Hash::make('password123'),
                'role'     => 'researcher',
                'email_verified_at' => now(),
            ]
        );
        $this->command->line("   ✅ Researcher: {$researcher->name} (ID: {$researcher->id})");

        // =============================================
        // 3. Create Rubric
        // =============================================
        $this->command->info('📋 Creating Rubric...');
        $rubric = Rubric::firstOrCreate(
            ['title' => 'Research Methodology Rubric (Test)'],
            [
                'mentor_id'   => $mentor->id,
                'description' => 'Rubric for evaluating research methodology submissions.',
                'is_global'   => false,
                'criteria'    => [
                    [
                        'name'        => 'Clarity of Research Question',
                        'description' => 'How clearly is the research question defined?',
                        'max_score'   => 25,
                    ],
                    [
                        'name'        => 'Literature Review',
                        'description' => 'Depth and relevance of the literature reviewed.',
                        'max_score'   => 25,
                    ],
                    [
                        'name'        => 'Methodology Design',
                        'description' => 'Appropriateness of the methodology chosen.',
                        'max_score'   => 25,
                    ],
                    [
                        'name'        => 'Data Analysis & Conclusion',
                        'description' => 'Quality of data analysis and conclusion drawn.',
                        'max_score'   => 25,
                    ],
                ],
            ]
        );
        $this->command->line("   ✅ Rubric: {$rubric->title} (ID: {$rubric->id})");

        // =============================================
        // 4. Create Task
        // =============================================
        $this->command->info('📝 Creating Task...');
        $task = Task::firstOrCreate(
            ['title' => 'Research Methodology Fundamentals (Test Task)'],
            [
                'mentor_id'   => $mentor->id,
                'rubric_id'   => $rubric->id,
                'description' => 'Write a comprehensive overview of research methodology fundamentals including qualitative and quantitative approaches. Explain the difference between primary and secondary research, and provide examples of each.',
                'deadline'    => now()->addDays(7),
                'video_url'   => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            ]
        );
        $this->command->line("   ✅ Task: {$task->title} (ID: {$task->id})");

        // =============================================
        // 5. Create Task Assignment
        // =============================================
        $this->command->info('🔗 Creating Task Assignment...');
        $assignment = TaskAssignment::firstOrCreate(
            ['task_id' => $task->id, 'researcher_id' => $researcher->id],
            [
                'status'       => 'submitted',
                'is_notified'  => true,
            ]
        );
        $this->command->line("   ✅ TaskAssignment ID: {$assignment->id}");

        // =============================================
        // 6. Create Submission
        // =============================================
        $this->command->info('📄 Creating Submission...');
        $submission = Submission::firstOrCreate(
            ['task_assignment_id' => $assignment->id],
            [
                'content_text' => "# Research Methodology Fundamentals

## Introduction
Research methodology is the systematic framework used by researchers to conduct a study. It defines the overall strategy and approach to investigation.

## Types of Research
### Qualitative Research
Qualitative research focuses on understanding the *why* and *how* behind phenomena. It uses methods such as:
- **Interviews**: One-on-one or group discussions to gather in-depth insights.
- **Focus Groups**: Structured discussions among a group of participants.
- **Ethnography**: Immersive observation within a community or setting.

### Quantitative Research
Quantitative research focuses on measuring and quantifying variables using statistical methods. Common approaches include:
- **Surveys**: Structured questionnaires with predefined answers.
- **Experiments**: Controlled studies to test cause-and-effect relationships.
- **Statistical Analysis**: Using tools like SPSS, R, or Python to analyze numerical data.

## Primary vs. Secondary Research
**Primary Research** involves collecting new, original data directly from sources. Examples:
- Conducting a survey on student satisfaction
- Running a lab experiment on compound interactions

**Secondary Research** involves analyzing existing data collected by others. Examples:
- Reviewing published academic journals
- Analyzing government census data

## Research Design Considerations
A robust research design should consider:
1. **Research Question**: Clearly define what you aim to investigate.
2. **Hypothesis**: Formulate testable predictions.
3. **Sampling Method**: Determine how participants will be selected.
4. **Data Collection**: Choose appropriate instruments and methods.
5. **Analysis Plan**: Decide on statistical or interpretive methods.

## Conclusion
Selecting the appropriate research methodology is critical to the validity and reliability of findings. Researchers must align their approach with the nature of their research question and available resources.",
                'original_filename' => 'research_methodology_fundamentals.md',
                'submitted_at'      => now(),
            ]
        );
        $this->command->line("   ✅ Submission ID: {$submission->id}");

        // =============================================
        // Summary
        // =============================================
        $this->command->newLine();
        $this->command->info('✅ All test data created successfully!');
        $this->command->newLine();
        $this->command->line('📊 <fg=cyan>Test Data Summary:</>');
        $this->command->table(
            ['Entity', 'ID', 'Name/Value'],
            [
                ['Mentor',       $mentor->id,     $mentor->name],
                ['Researcher',   $researcher->id, $researcher->name],
                ['Rubric',       $rubric->id,     $rubric->title],
                ['Task',         $task->id,       $task->title],
                ['Assignment',   $assignment->id, "Status: {$assignment->status}"],
                ['Submission',   $submission->id, "Content: " . substr($submission->content_text, 0, 40) . "..."],
            ]
        );

        // =============================================
        // 7. Trigger N8N Webhook
        // =============================================
        $this->command->newLine();
        $this->command->info('🔔 Triggering N8N webhook...');

        $ngrokUrl = env('APP_URL', 'http://127.0.0.1:8082');
        $n8nWebhook = 'https://svasyndicate.app.n8n.cloud/webhook-test/ai-assessment-trigger';

        $payload = [
            'submission_id'   => $submission->id,
            'task_id'         => $task->id,
            'rubric_id'       => $rubric->id,
            'researcher_id'   => $researcher->id,
            'assignment_id'   => $assignment->id,
            'laravel_api_url' => $ngrokUrl . '/api/n8n',
            'test_mode'       => true,
            'triggered_at'    => now()->toIso8601String(),
        ];

        try {
            $response = Http::timeout(15)->post($n8nWebhook, $payload);

            if ($response->successful()) {
                $this->command->info("   ✅ N8N Webhook triggered! Response: " . $response->body());
            } else {
                $this->command->warn("   ⚠️  Webhook responded with status: " . $response->status());
                $this->command->warn("   Response: " . $response->body());
            }
        } catch (\Exception $e) {
            $this->command->error("   ❌ Webhook error: " . $e->getMessage());
        }

        // =============================================
        // 8. Test Local API endpoints
        // =============================================
        $this->command->newLine();
        $this->command->info('🧪 Testing Local API Endpoints...');

        $baseUrl = 'http://127.0.0.1:8082/api/n8n';
        $endpoints = [
            ['GET', "/submissions/{$submission->id}",   "Fetch Submission Details"],
            ['GET', "/tasks/{$task->id}",               "Fetch Task Details"],
            ['GET', "/tasks/{$task->id}/rubric",        "Fetch Task Rubric"],
        ];

        foreach ($endpoints as [$method, $path, $label]) {
            try {
                $res = Http::timeout(5)->get($baseUrl . $path);
                $status = $res->successful() ? '✅' : '❌';
                $this->command->line("   {$status} [{$res->status()}] {$label}: {$baseUrl}{$path}");
            } catch (\Exception $e) {
                $this->command->error("   ❌ {$label}: " . $e->getMessage());
            }
        }

        $this->command->newLine();
        $this->command->info('🎉 N8N Full Test Complete!');
        $this->command->newLine();
        $this->command->line("📌 <fg=yellow>Important IDs for N8N:</>"); 
        $this->command->line("   submission_id  = <fg=green>{$submission->id}</>");
        $this->command->line("   task_id        = <fg=green>{$task->id}</>");
        $this->command->line("   rubric_id      = <fg=green>{$rubric->id}</>");
        $this->command->line("   ngrok_api_url  = <fg=green>{$ngrokUrl}/api/n8n</>");
    }
}
