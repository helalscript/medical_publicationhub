<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $invoice->invoice_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8fafc;
            color: #334155;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
        }
        .invoice-card {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            max-width: 850px;
            margin: 40px auto;
            padding: 40px;
        }
        .logo-placeholder {
            font-weight: 800;
            font-size: 24px;
            color: #0f172a;
            letter-spacing: -0.025em;
        }
        .invoice-title {
            font-weight: 700;
            color: #0f172a;
            font-size: 28px;
        }
        .table-custom th {
            font-weight: 700;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.05em;
            color: #64748b;
            border-bottom: 2px solid #e2e8f0;
            padding: 12px 8px;
        }
        .table-custom td {
            padding: 16px 8px;
            border-bottom: 1px solid #f1f5f9;
        }
        .total-box {
            background-color: #f8fafc;
            border-radius: 12px;
            padding: 20px;
        }
        @media print {
            body {
                background-color: #ffffff;
            }
            .invoice-card {
                border: none;
                box-shadow: none;
                padding: 0;
                margin: 0;
                max-width: 100%;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Action Buttons (No Print) -->
        <div class="text-center mt-4 no-print">
            <button onclick="window.print()" class="btn btn-primary px-4 py-2 me-2">Print / Save as PDF</button>
            <a href="{{ route('admin.payments.show', $payment->id) }}" class="btn btn-outline-secondary px-4 py-2">Back to Details</a>
        </div>

        <div class="invoice-card">
            <!-- Invoice Header -->
            <div class="row align-items-center mb-5">
                <div class="col-sm-6">
                    <div class="logo-placeholder">
                        @php
                            $setting = \App\Models\SystemSetting::first();
                        @endphp
                        {{ $setting->system_name ?? 'PublicationHub' }}
                    </div>
                    <p class="text-muted mb-0 small">Secure Web Payment Receipt</p>
                </div>
                <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
                    <div class="invoice-title">INVOICE</div>
                    <div class="fw-semibold text-muted">No: {{ $invoice->invoice_number }}</div>
                    <div class="text-muted small">Date: {{ $invoice->created_at->format('M d, Y H:i') }}</div>
                </div>
            </div>

            <hr class="my-4">

            <!-- Invoice Billing Details -->
            <div class="row mb-5">
                <div class="col-sm-6">
                    <h6 class="text-uppercase text-muted fw-bold fs-12 mb-3">Billed To</h6>
                    <div class="fw-bold text-dark fs-15">{{ $payment->user?->name ?? 'Guest User' }}</div>
                    <div class="text-muted">{{ $payment->user?->email ?? '-' }}</div>
                    @if($payment->user?->phone)
                        <div class="text-muted">{{ $payment->user->phone }}</div>
                    @endif
                </div>
                <div class="col-sm-6 text-sm-end mt-4 mt-sm-0">
                    <h6 class="text-uppercase text-muted fw-bold fs-12 mb-3">Payment Info</h6>
                    <div><span class="text-muted">Gateway:</span> <span class="fw-semibold">{{ ucfirst($payment->payment_gateway) }}</span></div>
                    <div><span class="text-muted">Gateway ID:</span> <span class="fw-semibold text-break">{{ $payment->payment_gateway_id }}</span></div>
                    <div><span class="text-muted">Status:</span> <span class="badge bg-success">Paid</span></div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="table-responsive mb-5">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th class="text-end" style="width: 150px;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                @php
                                    $payable = $payment->payable;
                                    $type = $payment->payable_type ? class_basename($payment->payable_type) : 'Service';
                                    $title = $payable?->title ?? $payable?->name ?? 'Service Access';
                                @endphp
                                <span class="fw-bold text-dark">{{ $type }}</span>
                                <div class="text-muted small mt-1">{{ $title }}</div>
                            </td>
                            <td class="text-end fw-semibold text-dark">{{ number_format($invoice->amount, 2) }} SAR</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Totals -->
            <div class="row justify-content-end">
                <div class="col-sm-5">
                    <div class="total-box">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal:</span>
                            <span class="fw-semibold">{{ number_format($invoice->amount, 2) }} SAR</span>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-dark">Grand Total:</span>
                            <span class="fw-bold text-primary fs-18">{{ number_format($invoice->amount, 2) }} SAR</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center text-muted mt-5 pt-4 border-top small">
                Thank you for your business. If you have any inquiries regarding this invoice, please reach out to us.
            </div>
        </div>
    </div>

    @if(isset($autoPrint) && $autoPrint)
        <script>
            window.onload = function() {
                window.print();
            }
        </script>
    @endif
</body>
</html>
