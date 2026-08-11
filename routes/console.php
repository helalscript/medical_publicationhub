<?php

use App\Console\Commands\LoanRepaymentStatusUpdate;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\Message;
use App\Events\MessageSent;
use App\Jobs\GenerateAndSendInvoicesJob;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new GenerateAndSendInvoicesJob)->everyFifteenMinutes();

// Run queue jobs via scheduler every minute (stops when queue is empty)
Schedule::command('queue:work --stop-when-empty')->everyMinute();

// Schedule::call(function () {
//     logger()->info('test-logger');
// })->everyMinute();

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote')->hourly();
// Schedule::call( function () {
//     logger()->info('test it');
// })->everySecond();

// Artisan::command('opportunity-chat:test {chatId}', function (string $chatId) {
//     $msg = Message::query()->where('chat_id', (int) $chatId)->latest('id')->first();
//     if (! $msg) {
//         $this->error('No message found for this chatId');
//         return;
//     }
//     event(new MessageSent($msg));
//     $this->info('Dispatched MessageSent for message #'.$msg->id);
// })->purpose('Dispatch chat message event');