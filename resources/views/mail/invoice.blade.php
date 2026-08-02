<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Receipt</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f8fafc;
            color: #334155;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }
        .header {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            padding: 32px;
            color: #ffffff;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: -0.025em;
        }
        .content {
            padding: 32px;
        }
        .welcome {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 24px;
        }
        .details-box {
            background-color: #f1f5f9;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 24px;
        }
        .details-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px;
        }
        .details-row:last-child {
            margin-bottom: 0;
        }
        .details-label {
            color: #64748b;
            font-weight: 500;
        }
        .details-value {
            font-weight: 600;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }
        .table th {
            text-align: left;
            padding: 12px 0;
            border-bottom: 2px solid #e2e8f0;
            color: #64748b;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .table td {
            padding: 16px 0;
            border-bottom: 1px solid #f1f5f9;
            font-size: 14px;
        }
        .table .price-col {
            text-align: right;
        }
        .total-section {
            border-top: 2px solid #e2e8f0;
            padding-top: 16px;
            display: flex;
            justify-content: flex-end;
        }
        .total-box {
            width: 200px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px;
        }
        .total-row.grand-total {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
            margin-top: 8px;
        }
        .footer {
            background-color: #f8fafc;
            padding: 24px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Payment Receipt</h1>
        </div>
        <div class="content">
            <p class="welcome">Hello, <strong>{{ $invoice->payment->user?->name }}</strong>. Thank you for your payment. Here are the details of your transaction:</p>
            
            <div class="details-box">
                <div class="details-row">
                    <span class="details-label">Invoice Number</span>
                    <span class="details-value">{{ $invoice->invoice_number }}</span>
                </div>
                <div class="details-row">
                    <span class="details-label">Date</span>
                    <span class="details-value">{{ $invoice->created_at->format('Y-m-d H:i') }}</span>
                </div>
                <div class="details-row">
                    <span class="details-label">Payment Gateway</span>
                    <span class="details-value">{{ ucfirst($invoice->payment->payment_gateway) }}</span>
                </div>
                <div class="details-row">
                    <span class="details-label">Transaction ID</span>
                    <span class="details-value">{{ $invoice->payment->payment_gateway_id }}</span>
                </div>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th class="price-col">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            @php
                                $payable = $invoice->payment->payable;
                                $type = $invoice->payment->payable_type ? class_basename($invoice->payment->payable_type) : 'Service';
                                $title = $payable?->title ?? $payable?->name ?? 'Service Access';
                            @endphp
                            <strong>{{ $type }}:</strong> {{ $title }}
                        </td>
                        <td class="price-col">{{ number_format($invoice->amount, 2) }} SAR</td>
                    </tr>
                </tbody>
            </table>

            <div class="total-section">
                <div class="total-box">
                    <div class="total-row">
                        <span class="details-label">Subtotal</span>
                        <span class="details-value">{{ number_format($invoice->amount, 2) }} SAR</span>
                    </div>
                    <div class="total-row grand-total">
                        <span>Total</span>
                        <span>{{ number_format($invoice->amount, 2) }} SAR</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer">
            <p>If you have any questions, please contact our support team.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
