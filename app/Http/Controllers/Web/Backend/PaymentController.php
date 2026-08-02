<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Payment::with(['user', 'payable'])->latest();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('user_name', function ($row) {
                    return e($row->user?->name ?? '-');
                })
                ->addColumn('payable_type_short', function ($row) {
                    $t = $row->payable_type ? class_basename($row->payable_type) : '-';
                    return '<span class="badge bg-secondary">'.$t.'</span>';
                })
                ->addColumn('payable_title', function ($row) {
                    return e($row->payable?->title ?? $row->payable?->name ?? '-');
                })
                ->addColumn('status_badge', function ($row) {
                    $map = [
                        'pending' => 'warning',
                        'paid' => 'success',
                        'failed' => 'danger',
                        'cancelled' => 'secondary',
                        'refunded' => 'info',
                    ];
                    $c = $map[$row->status] ?? 'secondary';
                    return '<span class="badge bg-'.$c.'">'.e($row->status).'</span>';
                })
                ->addColumn('action', function ($row) {
                    $user = auth()->user();
                    if (! $user || ! $user->can('admin.payments.show')) {
                        return '-';
                    }

                    return '<div class="action-wrapper">'
                        .'<a type="button" href="'.route('admin.payments.show', $row->id).'" class="ps-0 border-0 bg-transparent lh-1" title="View"><i class="material-symbols-outlined fs-16 text-primary">visibility</i></a>'
                        .'</div>';
                })
                ->rawColumns(['payable_type_short', 'status_badge', 'action'])
                ->make(true);
        }

        return view('backend.layouts.payments.index');
    }

    public function show(int $id)
    {
        $data = Payment::with(['user', 'payable'])->findOrFail($id);
        return view('backend.layouts.payments.show', compact('data'));
    }

    public function status(Request $request, int $id)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,paid,failed,cancelled,refunded'],
        ]);

        try {
            $p = Payment::findOrFail($id);
            $p->status = $validated['status'];
            $p->save();

            return redirect()->back()->with('success', 'Payment status updated.');
        } catch (Exception $e) {
            Log::error('PaymentController::status '.$e->getMessage());
            return redirect()->back()->with('error', 'Payment status update failed.');
        }
    }

    public function viewInvoice(int $id)
    {
        $payment = Payment::with(['user', 'payable', 'invoice'])->findOrFail($id);
        
        // If there's no invoice record but payment is paid, we generate it on-the-fly (fallback)
        if ($payment->status === 'paid' && !$payment->invoice) {
            $dateStr = \Carbon\Carbon::now()->format('Ymd');
            $lastInvoice = \App\Models\Invoice::where('invoice_number', 'like', "INV-{$dateStr}-%")
                ->orderBy('id', 'desc')
                ->first();

            $nextSeq = 1;
            if ($lastInvoice) {
                $parts = explode('-', $lastInvoice->invoice_number);
                $lastSeq = (int) end($parts);
                $nextSeq = $lastSeq + 1;
            }

            $invoiceNumber = 'INV-' . $dateStr . '-' . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);

            $invoice = \App\Models\Invoice::create([
                'payment_id' => $payment->id,
                'invoice_number' => $invoiceNumber,
                'amount' => $payment->amount,
            ]);
            $payment->setRelation('invoice', $invoice);
        }

        $invoice = $payment->invoice;
        if (!$invoice) {
            return redirect()->back()->with('error', 'No invoice available for unpaid/pending payments.');
        }

        return view('backend.layouts.payments.invoice', compact('payment', 'invoice'));
    }

    public function downloadInvoice(int $id)
    {
        $payment = Payment::with(['user', 'payable', 'invoice'])->findOrFail($id);
        
        // If there's no invoice record but payment is paid, generate it
        if ($payment->status === 'paid' && !$payment->invoice) {
            $dateStr = \Carbon\Carbon::now()->format('Ymd');
            $lastInvoice = \App\Models\Invoice::where('invoice_number', 'like', "INV-{$dateStr}-%")
                ->orderBy('id', 'desc')
                ->first();

            $nextSeq = 1;
            if ($lastInvoice) {
                $parts = explode('-', $lastInvoice->invoice_number);
                $lastSeq = (int) end($parts);
                $nextSeq = $lastSeq + 1;
            }

            $invoiceNumber = 'INV-' . $dateStr . '-' . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);

            $invoice = \App\Models\Invoice::create([
                'payment_id' => $payment->id,
                'invoice_number' => $invoiceNumber,
                'amount' => $payment->amount,
            ]);
            $payment->setRelation('invoice', $invoice);
        }

        $invoice = $payment->invoice;
        if (!$invoice) {
            return redirect()->back()->with('error', 'No invoice available for unpaid/pending payments.');
        }

        // Return printable invoice view with auto-print query parameter
        return view('backend.layouts.payments.invoice', [
            'payment' => $payment,
            'invoice' => $invoice,
            'autoPrint' => true
        ]);
    }
}
