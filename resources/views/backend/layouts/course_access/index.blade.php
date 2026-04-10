@extends('backend.app')
@section('title', 'Course Access')

@section('content')
    <div class="main-content-container overflow-hidden">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <h3 class="mb-0">Course Access</h3>
        </div>

        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card bg-white border-0 rounded-3">
                    <div class="card-body">
                        <h5 class="mb-3">Grant access</h5>
                        <form action="{{ route('admin.course_access.grant') }}" method="POST" class="row g-3">
                            @csrf
                            <div class="col-12">
                                <label class="form-label">User <span class="text-danger">*</span></label>
                                <select name="user_id" class="form-select" required>
                                    <option value="">Select user</option>
                                    @foreach($users as $u)
                                        <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Course <span class="text-danger">*</span></label>
                                <select name="course_id" class="form-select" required>
                                    <option value="">Select course</option>
                                    @foreach($courses as $c)
                                        <option value="{{ $c->id }}">{{ $c->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Access type <span class="text-danger">*</span></label>
                                <select name="access_type" class="form-select" required>
                                    <option value="admin_grant">Admin grant</option>
                                    <option value="paid">Paid</option>
                                    <option value="free">Free</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Expire at (optional)</label>
                                <input type="date" name="expire_at" class="form-control">
                            </div>
                            <div class="col-12">
                                <button class="btn btn-primary" type="submit">Grant</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card bg-white border-0 rounded-3">
                    <div class="card-body">
                        <h5 class="mb-3">Revoke access</h5>
                        <form action="{{ route('admin.course_access.revoke') }}" method="POST" class="row g-3">
                            @csrf
                            <div class="col-12">
                                <label class="form-label">User <span class="text-danger">*</span></label>
                                <select name="user_id" class="form-select" required>
                                    <option value="">Select user</option>
                                    @foreach($users as $u)
                                        <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Course <span class="text-danger">*</span></label>
                                <select name="course_id" class="form-select" required>
                                    <option value="">Select course</option>
                                    @foreach($courses as $c)
                                        <option value="{{ $c->id }}">{{ $c->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <button class="btn btn-outline-danger" type="submit">Revoke (set inactive)</button>
                            </div>
                        </form>
                        <small class="text-muted d-block mt-2">Revoke just marks `user_course_access.status` as inactive.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

