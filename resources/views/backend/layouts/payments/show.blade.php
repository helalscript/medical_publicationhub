@extends('backend.app')
@section('title', 'Payment Details')

@section('content')
    <div class="main-content-container overflow-hidden">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <h3 class="mb-0">Payment Details</h3>
            <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>

        <div class="card bg-white border-0 rounded-3 mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="text-muted">User</div>
                        <div class="fw-semibold">{{ $data->user?->name ?? '-' }} <span class="text-muted">({{ $data->user?->email ?? '-' }})</span></div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-muted">Amount</div>
                        <div class="fw-semibold">{{ $data->amount }}</div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-muted">Status</div>
                        <div class="fw-semibold text-capitalize">{{ $data->status }}</div>
                    </div>

                    <div class="col-md-6">
                        <div class="text-muted">Payable type</div>
                        <div class="fw-semibold">{{ $data->payable_type ? class_basename($data->payable_type) : '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted">Payable</div>
                        <div class="fw-semibold">{{ $data->payable?->title ?? $data->payable?->name ?? '-' }}</div>
                    </div>

                    <div class="col-md-4">
                        <div class="text-muted">Gateway</div>
                        <div class="fw-semibold">{{ $data->payment_gateway ?? '-' }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted">Gateway ID</div>
                        <div class="fw-semibold">{{ $data->payment_gateway_id ?? '-' }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted">Pay ID</div>
                        <div class="fw-semibold">{{ $data->pay_id ?? '-' }}</div>
                    </div>
                </div>

                @if($data->status === 'paid')
                    <hr class="my-4">
                    <div>
                        <h6 class="mb-3 text-muted">Invoice Actions</h6>
                        <a href="{{ route('admin.payments.invoice.view', $data->id) }}" target="_blank" class="btn btn-primary me-2">
                            View Invoice
                        </a>
                        <a href="{{ route('admin.payments.invoice.download', $data->id) }}" target="_blank" class="btn btn-outline-primary">
                            Print / Download PDF
                        </a>
                    </div>
                @endif

                <hr class="my-4">

                @can('admin.payments.status')
                    <form action="{{ route('admin.payments.status', $data->id) }}" method="POST" class="row g-2 align-items-end">
                        @csrf
                        <div class="col-md-4">
                            <label class="form-label">Update status</label>
                            <select name="status" class="form-select">
                                @foreach(['pending','paid','failed','cancelled','refunded'] as $s)
                                    <option value="{{ $s }}" @selected(old('status', $data->status) === $s)>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                @endcan
            </div>
        </div>
    </div>
@endsection

