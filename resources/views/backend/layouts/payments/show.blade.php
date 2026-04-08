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

                <hr class="my-4">

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
            </div>
        </div>
    </div>
@endsection

