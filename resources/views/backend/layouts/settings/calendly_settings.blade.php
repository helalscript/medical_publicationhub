@extends('backend.layouts.settings.layout')
@section('form_title', 'Calendly')
@section('form_description', 'Calendly integration')

@section('form_content')
    <form action="{{ route('system_settings.calendly_update') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-lg-12">
                <div class="alert alert-info">
                    Frontend will call the public API to get the scheduling URL.
                </div>
            </div>

            <div class="col-lg-6">
                <div class="form-group mb-4">
                    <label class="label text-secondary">Calendly Personal Access Token (optional)</label>
                    <div class="form-group position-relative">
                        <input type="text" class="form-control text-dark ps-5 h-55 @error('calendly_personal_access_token') is-invalid @enderror"
                               name="calendly_personal_access_token"
                               value="{{ old('calendly_personal_access_token', $settings->calendly_personal_access_token ?? '') }}"
                               placeholder="Paste token here">
                    </div>
                    @error('calendly_personal_access_token')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-lg-6">
                <div class="form-group mb-4">
                    <label class="label text-secondary">Scheduling URL <span class="text-danger">*</span></label>
                    <div class="form-group position-relative">
                        <input type="text" class="form-control text-dark ps-5 h-55 @error('calendly_scheduling_url') is-invalid @enderror"
                               name="calendly_scheduling_url"
                               value="{{ old('calendly_scheduling_url', $settings->calendly_scheduling_url ?? '') }}"
                               required
                               placeholder="https://calendly.com/your-org/meeting">
                    </div>
                    @error('calendly_scheduling_url')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-lg-6">
                <div class="form-group mb-4">
                    <label class="label text-secondary">Calendly User URI (optional)</label>
                    <div class="form-group position-relative">
                        <input type="text" class="form-control text-dark ps-5 h-55 @error('calendly_user_uri') is-invalid @enderror"
                               name="calendly_user_uri"
                               value="{{ old('calendly_user_uri', $settings->calendly_user_uri ?? '') }}"
                               placeholder="https://api.calendly.com/users/XXXX">
                    </div>
                    @error('calendly_user_uri')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-lg-6">
                <div class="form-group mb-4">
                    <label class="label text-secondary">Event Type URI (optional)</label>
                    <div class="form-group position-relative">
                        <input type="text" class="form-control text-dark ps-5 h-55 @error('calendly_event_type_uri') is-invalid @enderror"
                               name="calendly_event_type_uri"
                               value="{{ old('calendly_event_type_uri', $settings->calendly_event_type_uri ?? '') }}"
                               placeholder="https://api.calendly.com/event_types/XXXX">
                    </div>
                    @error('calendly_event_type_uri')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="d-flex flex-wrap gap-3">
                    <button type="submit" class="btn btn-primary py-2 px-4 fw-medium fs-16">
                        <i class="ri-check-line text-white fw-medium"></i> Update
                    </button>
                </div>
            </div>
        </div>
    </form>
@endsection

