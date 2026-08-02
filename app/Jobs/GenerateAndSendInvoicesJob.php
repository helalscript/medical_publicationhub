<?php

namespace App\Jobs;

use App\Mail\InvoiceMail;
use App\Models\Invoice;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class GenerateAndSendInvoicesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('[GenerateAndSendInvoicesJob] Start processing paid payments without invoices...');

        // Fetch all paid payments that do not have an associated invoice
        $payments = Payment::where('status', 'paid')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('invoices')
                    ->whereColumn('invoices.payment_id', 'payments.id');
            })
            ->with('user')
            ->get();

        Log::info('[GenerateAndSendInvoicesJob] Found ' . $payments->count() . ' payments to process.');

        foreach ($payments as $payment) {
            try {
                DB::transaction(function () use ($payment) {
                    // Generate unique invoice number
                    // e.g. INV-20260730-0001
                    $dateStr = Carbon::now()->format('Ymd');
                    $lastInvoice = Invoice::where('invoice_number', 'like', "INV-{$dateStr}-%")
                        ->orderBy('id', 'desc')
                        ->first();

                    $nextSeq = 1;
                    if ($lastInvoice) {
                        $parts = explode('-', $lastInvoice->invoice_number);
                        $lastSeq = (int) end($parts);
                        $nextSeq = $lastSeq + 1;
                    }

                    $invoiceNumber = 'INV-' . $dateStr . '-' . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);

                    $invoice = Invoice::create([
                        'payment_id' => $payment->id,
                        'invoice_number' => $invoiceNumber,
                        'amount' => $payment->amount,
                    ]);

                    if ($payment->user && $payment->user->email) {
                        Mail::to($payment->user->email)->queue(new InvoiceMail($invoice));
                        Log::info("[GenerateAndSendInvoicesJob] Queued InvoiceMail for payment ID {$payment->id}, Invoice: {$invoiceNumber}");
                    } else {
                        Log::warning("[GenerateAndSendInvoicesJob] Payment ID {$payment->id} user has no email address. Skipping email.");
                    }
                });
            } catch (\Exception $e) {
                Log::error("[GenerateAndSendInvoicesJob] Failed to generate invoice for payment ID {$payment->id}: " . $e->getMessage());
            }
        }

        Log::info('[GenerateAndSendInvoicesJob] Finished processing.');
    }
}
