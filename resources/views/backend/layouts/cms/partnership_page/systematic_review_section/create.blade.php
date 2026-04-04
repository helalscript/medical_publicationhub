@extends('backend.app')
@section('title', 'Systematic Review - Add Item')

@section('content')
    <div class="main-content-container overflow-hidden">
        <h3 class="mb-4">Systematic Review — Add Item</h3>
        <div class="card bg-white border-0 rounded-3 mb-4">
            <div class="card-body p-4">
                <form action="{{ route('cms.partnership_page.systematic_review_section.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="label text-secondary">Title</label>
                        <input type="text" name="title" class="form-control h-55" value="{{ old('title') }}">
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <a href="{{ route('cms.partnership_page.systematic_review_section.index') }}" class="btn btn-danger ms-2">Cancel</a>
                </form>
            </div>
        </div>
    </div>
@endsection
