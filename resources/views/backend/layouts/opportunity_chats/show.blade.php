@extends('backend.app')
@section('title', 'Chat')

@push('styles')
    <style>
        .chat-wrapper {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            border: 1px solid rgba(0,0,0,.05);
        }
        .chat-header {
            padding: 1.25rem;
            border-bottom: 1px solid rgba(0,0,0,.05);
            background: #f8fafc;
        }
        .chat-body {
            height: 550px;
            overflow-y: auto;
            padding: 1.5rem;
            background: #fff;
            display: flex;
            flex-direction: column;
        }
        .message-row {
            display: flex;
            margin-bottom: 1.25rem;
            width: 100%;
        }
        .message-row.mine {
            flex-direction: row-reverse;
        }
        .message-bubble {
            max-width: 70%;
            padding: 0.75rem 1rem;
            border-radius: 12px;
            position: relative;
            font-size: 0.95rem;
            line-height: 1.5;
        }
        .message-row.mine .message-bubble {
            background: #4f46e5;
            color: #fff;
            border-bottom-right-radius: 2px;
        }
        .message-row.others .message-bubble {
            background: #f1f5f9;
            color: #1e293b;
            border-bottom-left-radius: 2px;
        }
        .message-meta {
            font-size: 0.75rem;
            color: #64748b;
            margin-bottom: 0.25rem;
            display: block;
        }
        .message-row.mine .message-meta {
            text-align: right;
        }
        .message-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 0.5rem;
        }
        .sender-name {
            font-weight: 600;
            font-size: 0.8rem;
            margin-bottom: 2px;
            display: block;
        }
        .participant-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px;
            border-radius: 8px;
            transition: background 0.2s;
            cursor: pointer;
        }
        .participant-item:hover {
            background: #f1f5f9;
        }
        .participant-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
        }
        #load-more-trigger {
            text-align: center;
            padding: 10px;
            font-size: 12px;
            color: #64748b;
        }
    </style>
@endpush

@section('content')
    <div class="main-content-container overflow-hidden">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <div>
                <h3 class="mb-0">Chat #{{ $chat->id }}</h3>
                <p class="text-muted mb-0">{{ $chat->chatable?->title ?? '—' }}</p>
            </div>
            <a href="{{ route('admin.opportunity_chats.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>

        @if (session('success'))
            <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div>
        @endif

        <div class="row g-3">
            <div class="col-xl-8">
                <div class="chat-wrapper">
                    <div class="chat-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="fw-semibold text-dark">Discussion</span>
                            <span class="badge bg-soft-primary text-primary">{{ $chat->messages()->count() }} Messages</span>
                        </div>
                    </div>
                    <div class="chat-body" id="chatArea">
                        <div id="load-more-trigger">
                            <div class="spinner-border spinner-border-sm d-none" role="status"></div>
                            <span class="trigger-text">Scroll up to load more</span>
                        </div>
                        <div id="messageContainer" class="d-flex flex-column">
                            @foreach ($messages->reverse() as $m)
                                @php $isMine = $m->user_id === auth()->id(); @endphp
                                <div class="message-row {{ $isMine ? 'mine' : 'others' }}">
                                    @if(!$isMine)
                                        <img src="{{ $m->user?->avatar ? asset($m->user->avatar) : asset('backend/admin/assets/images/avatar_defult.png') }}" class="message-avatar" alt="">
                                    @endif
                                    <div class="flex-grow-1" style="max-width: 70%">
                                        <span class="message-meta">
                                            @if(!$isMine) <span class="sender-name">{{ $m->user?->name ?? 'User' }}</span> @endif
                                            {{ $m->created_at?->diffForHumans() }}
                                        </span>
                                        <div class="message-bubble shadow-sm">
                                            {{ $m->message }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="p-3 border-top bg-light">
                        <form action="{{ route('admin.opportunity_chats.messages.store', $chat->id) }}" method="POST">
                            @csrf
                            <div class="input-group">
                                <textarea name="message" rows="1" class="form-control border-0 shadow-none" placeholder="Write a message..." required style="resize: none;"></textarea>
                                <button type="submit" class="btn btn-primary d-flex align-items-center px-3">
                                    <i class="material-symbols-outlined fs-20">send</i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card border-0 rounded-3 shadow-sm bg-white">
                    <div class="card-body p-4 ">
                        <h6 class="mb-3 card-header">Participants</h6>
                        <div class="d-flex flex-column gap-2">
                            @foreach ($chat->users as $cu)
                                @php $u = $cu->user; @endphp
                                <div class="participant-item" onclick="showParticipantModal({{ json_encode([
                                    'name' => $u?->name ?? 'User',
                                    'email' => $u?->email ?? '-',
                                    'phone' => $u?->phone ?? '-',
                                    'avatar' => $u?->avatar ? asset($u->avatar) : asset('backend/admin/assets/images/avatar_defult.png'),
                                    'role' => $cu->role ?? ($u?->role ?? 'User'),
                                    'institution' => $u?->institution ?? '-'
                                ]) }})">
                                    <img src="{{ $u?->avatar ? asset($u->avatar) : asset('backend/admin/assets/images/avatar_defult.png') }}" class="participant-avatar" alt="">
                                    <div>
                                        <div class="fw-semibold small">{{ $u?->name ?? $u?->email ?? '—' }}</div>
                                        <div class="text-muted smaller">{{ $cu->role }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Participant Modal -->
    <div class="modal fade" id="participantModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-body p-4">
                    <div class="text-center mb-4">
                        <img id="p-avatar" src="" alt="" class="rounded-circle shadow-sm border" style="width: 80px; height: 80px; object-fit: cover;">
                        <h5 id="p-name" class="mt-3 mb-1"></h5>
                        <span id="p-role" class="badge bg-soft-primary text-primary px-3"></span>
                    </div>
                    <div class="vstack gap-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="material-symbols-outlined text-muted fs-20">mail</i>
                            <div>
                                <div class="text-muted small">Email</div>
                                <div id="p-email" class="fw-medium"></div>
                            </div>
                        </div>
                        <!-- <div class="d-flex align-items-center gap-2">
                            <i class="material-symbols-outlined text-muted fs-20">call</i>
                            <div>
                                <div class="text-muted small">Phone</div>
                                <div id="p-phone" class="fw-medium"></div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <i class="material-symbols-outlined text-muted fs-20">apartment</i>
                            <div>
                                <div class="text-muted small">Institution</div>
                                <div id="p-institution" class="fw-medium"></div>
                            </div>
                        </div> -->
                    </div>
                    <button type="button" class="btn btn-light w-100 mt-4 h-45" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chatArea = document.getElementById('chatArea');
            const messageContainer = document.getElementById('messageContainer');
            const loader = document.querySelector('#load-more-trigger .spinner-border');
            const triggerText = document.querySelector('#load-more-trigger .trigger-text');
            
            let currentPage = 1;
            let lastPage = {{ $messages->lastPage() }};
            let isLoading = false;

            // Scroll to bottom on init
            chatArea.scrollTop = chatArea.scrollHeight;

            chatArea.addEventListener('scroll', function() {
                if (chatArea.scrollTop === 0 && !isLoading && currentPage < lastPage) {
                    loadMoreMessages();
                }
            });

            function loadMoreMessages() {
                isLoading = true;
                loader.classList.remove('d-none');
                triggerText.classList.add('d-none');
                
                const oldHeight = chatArea.scrollHeight;
                currentPage++;

                fetch(`{{ route('admin.opportunity_chats.show', $chat->id) }}?page=${currentPage}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => response.json())
                .then(data => {
                    const messages = data.data; // latest first
                    const html = messages.reverse().map(m => createMessageHtml(m)).join('');
                    messageContainer.insertAdjacentHTML('afterbegin', html);
                    
                    // Maintain scroll position
                    chatArea.scrollTop = chatArea.scrollHeight - oldHeight;
                    
                    isLoading = false;
                    loader.classList.add('d-none');
                    triggerText.classList.remove('d-none');

                    if (currentPage >= lastPage) {
                        document.getElementById('load-more-trigger').classList.add('d-none');
                    }
                })
                .catch(err => {
                    console.error(err);
                    isLoading = false;
                    loader.classList.add('d-none');
                    triggerText.classList.remove('d-none');
                });
            }

            function createMessageHtml(m) {
                const isMine = m.user_id == {{ auth()->id() }};
                const defaultAvatar = '{{ asset('backend/admin/assets/images/avatar_defult.png') }}';
                let avatar = defaultAvatar;
                
                if (m.user?.avatar) {
                    if (m.user.avatar.startsWith('http')) {
                        avatar = m.user.avatar;
                    } else if (m.user.avatar.startsWith('uploads/')) {
                        avatar = '/' + m.user.avatar;
                    } else {
                        avatar = '/storage/' + m.user.avatar;
                    }
                }
                
                const name = m.user?.name || 'User';
                const date = new Date(m.created_at).toLocaleString();
                
                return `
                    <div class="message-row ${isMine ? 'mine' : 'others'}">
                        ${!isMine ? `<img src="${avatar}" class="message-avatar" alt="">` : ''}
                        <div class="flex-grow-1" style="max-width: 70%">
                            <span class="message-meta">
                                ${!isMine ? `<span class="sender-name">${name}</span>` : ''}
                                ${date}
                            </span>
                            <div class="message-bubble shadow-sm">
                                ${m.message}
                            </div>
                        </div>
                    </div>
                `;
            }
        });

        window.showParticipantModal = function(data) {
            document.getElementById('p-avatar').src = data.avatar;
            document.getElementById('p-name').innerText = data.name;
            document.getElementById('p-role').innerText = data.role;
            document.getElementById('p-email').innerText = data.email;
            //document.getElementById('p-phone').innerText = data.phone;
            //document.getElementById('p-institution').innerText = data.institution;
            
            const modal = new bootstrap.Modal(document.getElementById('participantModal'));
            modal.show();
        };
    </script>
@endpush
