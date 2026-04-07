@extends('backend.app')
@section('title', 'Chat')

@section('content')
    <div class="main-content-container overflow-hidden">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <div>
                <h3 class="mb-0">Chat #{{ $chat->id }}</h3>
                <small class="text-muted">{{ $chat->chatable?->title ?? '—' }}</small>
            </div>
            <a href="{{ route('admin.opportunity_chats.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="row g-3">
            <div class="col-xl-8">
                <div class="card bg-white border-0 rounded-3">
                    <div class="card-body p-4">
                        <div class="d-flex flex-column gap-3" style="max-height: 520px; overflow:auto;">
                            @foreach ($messages as $m)
                                <div class="p-3 rounded-3 {{ $m->user_id === auth()->id() ? 'bg-light' : 'bg-white border' }}">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <strong>{{ $m->user?->name ?? $m->user?->email ?? '—' }}</strong>
                                        <small class="text-muted">{{ $m->created_at?->format('Y-m-d H:i') }}</small>
                                    </div>
                                    <div>{{ $m->message }}</div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-3">
                            {{ $messages->links() }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card bg-white border-0 rounded-3">
                    <div class="card-body p-4">
                        <h6 class="mb-3">Participants</h6>
                        <ul class="mb-3">
                            @foreach ($chat->users as $cu)
                                <li>{{ $cu->user?->name ?? $cu->user?->email ?? '—' }} @if($cu->role) ({{ $cu->role }}) @endif</li>
                            @endforeach
                        </ul>

                        <h6 class="mb-2">Send message</h6>
                        <form action="{{ route('admin.opportunity_chats.messages.store', $chat->id) }}" method="POST">
                            @csrf
                            <textarea name="message" rows="4" class="form-control" placeholder="Type message..." required>{{ old('message') }}</textarea>
                            <button class="btn btn-primary w-100 mt-2">Send</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

