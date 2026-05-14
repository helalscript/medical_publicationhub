<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users to seed notifications for each
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('No users found in the database. Please seed users first.');
            return;
        }

        foreach ($users as $user) {
            $this->command->info("Seeding 1000 notifications for user: {$user->email} (ID: {$user->id})");
            
            $notifications = [];
            for ($i = 1; $i <= 1000; $i++) {
                $notifications[] = [
                    'id' => (string) Str::uuid(),
                    'type' => 'App\\Notifications\\WelcomeNotification',
                    'notifiable_type' => 'App\\Models\\User',
                    'notifiable_id' => $user->id,
                    'data' => json_encode([
                        'title' => 'Welcome to Our Platform (' . $i . ')',
                        'message' => 'Thank you for registering with us. We are excited to have you!',
                        'type' => 'welcome',
                        'image' => url('notification-icon/png-transparent-tea-cup-color-gr.png'),
                        'deep_link' => [
                            'app' => 'myapp://welcome',
                            'web' => url('/welcome')
                        ],
                        'data' => [
                            'user_id' => $user->id,
                            'email' => $user->email,
                        ],
                        'footer' => 'Thanks for joining us!',
                        'timestamp' => now()->toISOString(),
                    ]),
                    'read_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // Bulk insert in chunks of 500 to optimize performance and memory
                if (count($notifications) >= 500) {
                    DB::table('notifications')->insert($notifications);
                    $notifications = [];
                }
            }

            // Insert remaining notifications for the current user
            if (!empty($notifications)) {
                DB::table('notifications')->insert($notifications);
            }
        }

        $this->command->info('Notification seeding completed successfully.');
    }
}
