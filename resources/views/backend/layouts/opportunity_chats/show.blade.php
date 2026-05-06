@extends('backend.app')
@section('title', 'Chat')

@push('styles')
    <style>
        .chat-wrapper {
            background: var(--bs-card-bg, #fff);
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            border: 1px solid var(--bs-border-color, rgba(0,0,0,.05));
        }
        .chat-header {
            padding: 1.25rem;
            border-bottom: 1px solid var(--bs-border-color, rgba(0,0,0,.05));
            background: var(--bs-tertiary-bg, #f8fafc);
        }
        .chat-body {
            height: 550px;
            overflow-y: auto;
            padding: 1.5rem;
            background: var(--bs-card-bg, #fff);
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
            background: var(--bs-tertiary-bg, #f1f5f9);
            color: var(--bs-body-color, #1e293b);
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
        .chat-footer {
            background: var(--bs-tertiary-bg, #f8fafc);
        }
        textarea.form-control {
            background: transparent !important;
            color: var(--bs-body-color) !important;
        }
        textarea.form-control::placeholder {
            color: var(--bs-secondary-color);
        }

        /* Dark Mode Overrides */
        body[data-theme="dark"] .chat-wrapper {
            background: #15203c !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
        }
        body[data-theme="dark"] .chat-header {
            background: #0b1120 !important;
            border-bottom-color: rgba(255, 255, 255, 0.1) !important;
        }
        body[data-theme="dark"] .chat-header .text-dark {
            color: #f1f5f9 !important;
        }
        body[data-theme="dark"] .chat-body {
            background: #15203c !important;
        }
        body[data-theme="dark"] .message-row.others .message-bubble {
            background: #1e294b !important;
            color: #f1f5f9 !important;
        }
        body[data-theme="dark"] .message-meta {
            color: #94a3b8 !important;
        }
        body[data-theme="dark"] .chat-footer {
            background: #0b1120 !important;
            border-top-color: rgba(255, 255, 255, 0.1) !important;
        }
        body[data-theme="dark"] .card {
            background: #15203c !important;
            color: #f1f5f9 !important;
        }
        body[data-theme="dark"] .participant-item:hover {
            background: #1e294b !important;
        }
        body[data-theme="dark"] .text-muted {
            color: #94a3b8 !important;
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
                                            @if ($m->message)
                                                <p class="mb-0">{{ $m->message }}</p>
                                            @endif
                                            @if ($m->file)
                                                <div class="message-file {{ $m->message ? 'mt-2' : '' }}">
                                                    @php $ext = pathinfo($m->file, PATHINFO_EXTENSION); @endphp
                                                    @if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                                        <a href="{{ asset($m->file) }}" target="_blank">
                                                            <img src="{{ asset($m->file) }}" class="img-fluid rounded" style="max-height: 200px" alt="file">
                                                        </a>
                                                    @else
                                                        <a href="{{ asset($m->file) }}" target="_blank" class="d-flex align-items-center gap-2 text-decoration-none {{ $isMine ? 'text-white' : 'text-primary' }}">
                                                            <i class="material-symbols-outlined">description</i>
                                                            <span>Download File</span>
                                                        </a>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="p-3 border-top chat-footer">
                        <form id="chatForm" action="{{ route('admin.opportunity_chats.messages.store', $chat->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="input-group align-items-center">
                                <label class="btn btn-link text-muted p-2" for="fileInput" style="text-decoration: none;">
                                    <i class="material-symbols-outlined fs-24">attach_file</i>
                                </label>
                                <input type="file" name="file" id="fileInput" class="d-none">
                                <textarea name="message" rows="1" class="form-control border-0 shadow-none" placeholder="Write a message..." style="resize: none;"></textarea>
                                <button type="submit" class="btn btn-primary d-flex align-items-center px-3 ms-2 rounded-3">
                                    <i class="material-symbols-outlined fs-20">send</i>
                                </button>
                            </div>
                            <div id="file-preview" class="mt-2 small text-muted d-none">
                                <span class="file-name"></span>
                                <button type="button" class="btn btn-link btn-sm text-danger p-0 ms-2" onclick="clearFile()">Remove</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card border-0 rounded-3 shadow-sm">
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

                let fileHtml = '';
                if (m.file) {
                    const fileUrl = m.file.startsWith('http') ? m.file : '/' + m.file;
                    const ext = m.file.split('.').pop().toLowerCase();
                    if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
                        fileHtml = `<div class="message-file ${m.message ? 'mt-2' : ''}">
                            <a href="${fileUrl}" target="_blank">
                                <img src="${fileUrl}" class="img-fluid rounded" style="max-height: 200px" alt="file">
                            </a>
                        </div>`;
                    } else {
                        fileHtml = `<div class="message-file ${m.message ? 'mt-2' : ''}">
                            <a href="${fileUrl}" target="_blank" class="d-flex align-items-center gap-2 text-decoration-none ${isMine ? 'text-white' : 'text-primary'}">
                                <i class="material-symbols-outlined">description</i>
                                <span>Download File</span>
                            </a>
                        </div>`;
                    }
                }
                
                return `
                    <div class="message-row ${isMine ? 'mine' : 'others'}">
                        ${!isMine ? `<img src="${avatar}" class="message-avatar" alt="">` : ''}
                        <div class="flex-grow-1" style="max-width: 70%">
                            <span class="message-meta">
                                ${!isMine ? `<span class="sender-name">${name}</span>` : ''}
                                ${date}
                            </span>
                            <div class="message-bubble shadow-sm">
                                ${m.message ? `<p class="mb-0">${m.message}</p>` : ''}
                                ${fileHtml}
                            </div>
                        </div>
                    </div>
                `;
            }

            
            // Echo Listener for real-time updates
            if (window.Echo) {
                window.Echo.private('opportunity_chat.{{ $chat->id }}')
                    .listen('.opportunity.chat', (e) => {
                        // console.log('Message Received =========================================', e);
                        if (e.message && e.message.user_id != {{ auth()->id() }}) {
                            const html = createMessageHtml(e.message);
                            messageContainer.insertAdjacentHTML('beforeend', html);
                            chatArea.scrollTop = chatArea.scrollHeight;
                        }
                    });
            }
            
            // Handle AJAX form submission
            const chatForm = document.getElementById('chatForm');
            const fileInput = document.getElementById('fileInput');
            const filePreview = document.getElementById('file-preview');
            const fileNameSpan = filePreview.querySelector('.file-name');

            if (fileInput) {
                fileInput.addEventListener('change', function() {
                    if (this.files && this.files[0]) {
                        fileNameSpan.textContent = this.files[0].name;
                        filePreview.classList.remove('d-none');
                    } else {
                        clearFile();
                    }
                });
            }

            window.clearFile = function() {
                fileInput.value = '';
                filePreview.classList.add('d-none');
                fileNameSpan.textContent = '';
            };

            if (chatForm) {
                chatForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(chatForm);
                    // console.log('Sending message with file:', formData.get('file'));
                    const textarea = chatForm.querySelector('textarea[name="message"]');
                    const submitBtn = chatForm.querySelector('button[type="submit"]');
                    
                    if (!textarea.value.trim() && !fileInput.files.length) return;
                    
                    submitBtn.disabled = true;
                    
                    fetch(chatForm.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const html = createMessageHtml(data.message);
                            messageContainer.insertAdjacentHTML('beforeend', html);
                            chatArea.scrollTop = chatArea.scrollHeight;
                            textarea.value = '';
                            clearFile();
                        }
                        submitBtn.disabled = false;
                    })
                    .catch(err => {
                        console.error(err);
                        submitBtn.disabled = false;
                    });
                });
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
