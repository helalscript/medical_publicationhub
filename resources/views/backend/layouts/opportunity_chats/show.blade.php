@extends('backend.app')
@section('title', 'Chats')

@push('styles')
    <style>
        /* Modern Premium Chat Design */
        .chat-container-layout {
            display: flex;
            gap: 24px;
            height: calc(100vh - 210px); /* Fit within main viewport */
            min-height: 580px;
            margin-bottom: 24px;
        }

        .chat-sidebar-panel {
            flex: 0 0 320px;
            width: 320px;
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
            border: 1px solid #eef2f7;
            display: flex;
            flex-direction: column;
            padding: 24px 16px;
            height: 100%;
        }

        .chat-main-panel {
            flex: 1;
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
            border: 1px solid #eef2f7;
            display: flex;
            flex-direction: column;
            height: 100%;
            overflow: hidden;
        }

        /* Conversation sidebar styles */
        .chat-sidebar-header {
            margin-bottom: 16px;
        }

        .chat-back-link {
            color: #64748b;
            font-size: 0.85rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 16px;
            font-weight: 500;
            transition: color 0.2s;
        }

        .chat-back-link:hover {
            color: #0cbab8;
        }

        .chat-sidebar-title {
            font-size: 1.35rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 16px;
        }

        .chat-search-bar {
            position: relative;
            margin-bottom: 16px;
        }

        .chat-search-bar input {
            width: 100%;
            padding: 10px 16px 10px 40px;
            border: 1px solid #eef2f7;
            border-radius: 12px;
            outline: none;
            font-size: 0.9rem;
            background: #f8fafc;
            color: #0f172a;
            transition: border-color 0.2s;
        }

        .chat-search-bar input:focus {
            border-color: #0cbab8;
        }

        .chat-search-bar .search-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 18px;
        }

        html[dir="rtl"] .chat-search-bar input {
            padding: 10px 40px 10px 16px;
        }

        html[dir="rtl"] .chat-search-bar .search-icon {
            left: auto;
            right: 14px;
        }

        .chat-tabs {
            display: flex;
            background: #f1f5f9;
            padding: 4px;
            border-radius: 10px;
            gap: 4px;
            margin-bottom: 16px;
        }

        .chat-tab-btn {
            flex: 1;
            border: none;
            background: transparent;
            color: #64748b;
            font-size: 0.85rem;
            font-weight: 600;
            padding: 8px 0;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            text-align: center;
        }

        .chat-tab-btn.active {
            background: #0cbab8;
            color: #fff;
            box-shadow: 0 2px 8px rgba(12, 186, 184, 0.2);
        }

        .conversations-scroll {
            flex: 1;
            overflow-y: auto;
            margin-right: -8px;
            padding-right: 8px;
        }

        html[dir="rtl"] .conversations-scroll {
            margin-right: 0;
            margin-left: -8px;
            padding-right: 0;
            padding-left: 8px;
        }

        .conversation-item {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 14px;
            border-radius: 16px;
            text-decoration: none !important;
            transition: all 0.2s ease;
            margin-bottom: 8px;
            border: 1px solid transparent;
        }

        .conversation-item:hover {
            background: #f8fafc;
        }

        .conversation-item.active {
            background: #f0fbfb;
            border-radius: 16px;
        }

        .conversation-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background-color: #0cbab8;
            border-top-left-radius: 16px;
            border-bottom-left-radius: 16px;
        }

        html[dir="rtl"] .conversation-item.active::before {
            left: auto;
            right: 0;
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
            border-top-right-radius: 16px;
            border-bottom-right-radius: 16px;
        }

        .conversation-left {
            display: flex;
            align-items: center;
            gap: 12px;
            overflow: hidden;
            flex: 1;
        }

        .conversation-icon-container {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            background-color: rgba(96, 93, 255, 0.1);
            color: #605dff;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .conversation-icon-container span {
            font-size: 20px;
        }

        .conversation-details {
            overflow: hidden;
            flex: 1;
        }

        .conversation-name {
            font-size: 0.9rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0 0 2px 0;
        }

        .conversation-item.active .conversation-name {
            color: #0c8886;
        }

        .conversation-preview {
            font-size: 0.78rem;
            color: #64748b;
            margin: 0;
        }

        .conversation-right {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            margin-left: 8px;
            flex-shrink: 0;
        }

        html[dir="rtl"] .conversation-right {
            margin-left: 0;
            margin-right: 8px;
            align-items: flex-start;
        }

        .conversation-meta-time {
            font-size: 0.72rem;
            color: #94a3b8;
            margin-bottom: 4px;
        }

        .conversation-unread-badge {
            min-width: 18px;
            height: 18px;
            border-radius: 50%;
            background-color: #ef4444;
            color: #fff;
            font-size: 0.65rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 4px;
        }

        /* Active chat panel styles */
        .chat-main-header {
            padding: 20px 24px;
            border-bottom: 1px solid #eef2f7;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .chat-main-header-info {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .chat-main-header-icon {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            background-color: rgba(96, 93, 255, 0.1);
            color: #605dff;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .chat-main-header-icon span {
            font-size: 24px;
        }

        .chat-main-header-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 2px 0;
        }

        .chat-main-header-members {
            font-size: 0.8rem;
            color: #64748b;
        }

        .chat-main-body {
            flex: 1;
            overflow-y: auto;
            padding: 24px;
            background: #f8fafc;
            display: flex;
            flex-direction: column;
        }

        /* Message bubbles */
        .msg-row {
            display: flex;
            margin-bottom: 20px;
            width: 100%;
            gap: 12px;
            align-items: flex-start;
        }

        .msg-row.mine {
            flex-direction: row-reverse;
        }

        .msg-avatar-img {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
        }

        .msg-avatar-initial {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            color: #fff;
            font-size: 0.85rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .msg-content-wrapper {
            max-width: 70%;
            display: flex;
            flex-direction: column;
        }

        .msg-row.mine .msg-content-wrapper {
            align-items: flex-end;
        }

        .msg-row.others .msg-content-wrapper {
            align-items: flex-start;
        }

        .msg-sender-name {
            font-size: 0.8rem;
            font-weight: 600;
            color: #64748b;
            margin-bottom: 4px;
        }

        .msg-bubble {
            padding: 12px 18px;
            border-radius: 16px;
            font-size: 0.92rem;
            line-height: 1.5;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.01);
            word-break: break-word;
        }

        .msg-row.mine .msg-bubble {
            background: #605dff;
            color: #fff;
            border-top-right-radius: 4px;
        }

        html[dir="rtl"] .msg-row.mine .msg-bubble {
            border-top-right-radius: 16px;
            border-top-left-radius: 4px;
        }

        .msg-row.others .msg-bubble {
            background: #fff;
            color: #1e293b;
            border-top-left-radius: 4px;
            border: 1px solid #eef2f7;
        }

        html[dir="rtl"] .msg-row.others .msg-bubble {
            border-top-left-radius: 16px;
            border-top-right-radius: 4px;
        }

        .msg-timestamp {
            font-size: 0.72rem;
            color: #94a3b8;
            margin-top: 4px;
        }

        /* Message input footer styling */
        .chat-main-footer {
            padding: 20px 24px;
            background: #fff;
            border-top: 1px solid #eef2f7;
        }

        .chat-input-container {
            display: flex;
            align-items: center;
            background: #fff;
            border: 1px solid #eef2f7;
            border-radius: 24px;
            padding: 6px 14px;
            gap: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        }

        .chat-input-container textarea {
            flex: 1;
            border: none;
            background: transparent;
            outline: none;
            resize: none;
            font-size: 0.92rem;
            color: #0f172a;
            max-height: 80px;
            padding: 8px 0;
            line-height: 1.4;
        }

        .chat-input-container textarea::placeholder {
            color: #94a3b8;
        }

        .chat-action-btn {
            color: #64748b;
            cursor: pointer;
            transition: color 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            background: transparent;
            border: none;
            padding: 4px;
        }

        .chat-action-btn:hover {
            color: #0cbab8;
        }

        .chat-send-btn {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background-color: #605dff;
            color: #fff;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            flex-shrink: 0;
        }

        .chat-send-btn:hover {
            background-color: #4f46e5;
            transform: scale(1.05);
        }

        .chat-send-btn span {
            font-size: 18px;
        }

        /* Dark mode styling overrides */
        body[data-theme="dark"] .chat-sidebar-panel,
        body[data-theme="dark"] .chat-main-panel,
        body[data-theme="dark"] .chat-main-header,
        body[data-theme="dark"] .chat-main-footer {
            background: #0f172a;
            border-color: #1e293b;
        }

        body[data-theme="dark"] .chat-sidebar-title,
        body[data-theme="dark"] .conversation-name {
            color: #f1f5f9;
        }

        body[data-theme="dark"] .conversation-item:hover {
            background: #1e294b;
        }

        body[data-theme="dark"] .conversation-item.active {
            background: rgba(12, 186, 184, 0.15);
        }

        body[data-theme="dark"] .conversation-item.active .conversation-name {
            color: #0dbab8;
        }

        body[data-theme="dark"] .chat-search-bar input {
            background: #1e294b;
            border-color: #1e293b;
            color: #f1f5f9;
        }

        body[data-theme="dark"] .chat-tabs {
            background: #1e294b;
        }

        body[data-theme="dark"] .chat-input-container {
            background: #0b1120;
            border-color: #1e293b;
            box-shadow: none;
        }

        body[data-theme="dark"] .chat-input-container textarea {
            color: #f1f5f9;
        }

        body[data-theme="dark"] .chat-main-body {
            background: #0b1120;
        }

        body[data-theme="dark"] .msg-row.others .msg-bubble {
            background: #1e294b;
            color: #f1f5f9;
            border-color: #1e293b;
        }

        /* Voice Rec Styling */
        .recording-indicator {
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            padding: 6px 12px;
            border-radius: 10px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .recording-pulse {
            width: 8px;
            height: 8px;
            background-color: #ef4444;
            border-radius: 50%;
            animation: pulse 1.5s infinite;
        }

        @media (max-width: 991px) {
            .chat-container-layout {
                height: calc(100vh - 270px);
                min-height: 480px;
                gap: 0;
            }

            .chat-sidebar-panel {
                flex: 1;
                width: 100%;
                border-radius: 20px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
            }

            .chat-main-panel {
                flex: 1;
                width: 100%;
                border-radius: 20px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
            }

            /* Toggle visibility based on sidebar-visible class on the container */
            .chat-container-layout:not(.sidebar-visible) .chat-sidebar-panel {
                display: none !important;
            }

            .chat-container-layout.sidebar-visible .chat-main-panel {
                display: none !important;
            }
            
            .chat-container-layout.sidebar-visible .chat-sidebar-panel {
                display: flex !important;
            }
        }

        @media (max-width: 767px) {
            .chat-container-layout {
                height: calc(100vh - 290px);
            }
            .chat-main-header {
                padding: 12px 16px;
            }
            .chat-main-body {
                padding: 16px;
            }
            .chat-main-footer {
                padding: 12px 16px;
            }
            .msg-content-wrapper {
                max-width: 85%;
            }
        }
    </style>
@endpush

@section('content')
    <div class="main-content-container overflow-hidden">
        {{-- Custom Title & Breadcrumbs matching style --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-0 fw-bold fs-20">{{ __('admin.chat.dashboard_overview') }}</h4>
                <p class="text-muted small mb-0">{{ __('admin.chat.overview_desc') }}</p>
            </div>
        </div>
        <div class="mb-3">
            <span class="text-muted small">{{ __('admin.menu.dashboard') }}</span>
            <span class="text-muted small mx-1">»</span>
            <span class="text-muted small">{{ __('admin.menu.opportunities') }}</span>
            <span class="text-muted small mx-1">»</span>
            <span class="text-muted small fw-bold text-primary">{{ __('admin.chat.title') }}</span>
        </div>

        <h3 class="fw-bold mb-4">{{ __('admin.chat.title') }}</h3>

        <div class="chat-container-layout">
            {{-- Left Conversation Sidebar --}}
            <div class="chat-sidebar-panel">
                <div class="chat-sidebar-header">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <a href="{{ route('admin.dashboard') }}" class="chat-back-link mb-0">
                            <span class="material-symbols-outlined align-middle fs-16">arrow_back</span>
                            {{ __('admin.chat.back_dashboard') }}
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-secondary d-lg-none py-1 px-2 border-0" id="mobileCloseSidebarBtn" style="border-radius: 8px;">
                            <span class="material-symbols-outlined align-middle fs-18">close</span>
                        </button>
                    </div>
                    <h4 class="chat-sidebar-title">{{ __('admin.chat.conversation') }}</h4>
                    <div class="chat-search-bar">
                        <input type="text" id="chatSearchInput" placeholder="{{ __('admin.chat.search_placeholder') }}">
                        <span class="material-symbols-outlined search-icon">search</span>
                    </div>
                    <div class="chat-tabs">
                        <button type="button" class="chat-tab-btn active" data-type="all">{{ __('admin.chat.all') }}</button>
                        <button type="button" class="chat-tab-btn" data-type="single">{{ __('admin.chat.private') }}</button>
                        <button type="button" class="chat-tab-btn" data-type="group">{{ __('admin.chat.groups') }}</button>
                    </div>
                </div>

                <div class="conversations-scroll" id="conversationsContainer">
                    @forelse($chats as $c)
                        @php
                            $isActive = $c->id === $chat->id;
                            $lastMsg = $c->last_message;
                            $timeStr = $lastMsg ? $lastMsg->created_at?->format('h:i A') : '';
                            if ($lastMsg && $lastMsg->created_at?->isYesterday()) {
                                $timeStr = __('admin.chat.yesterday');
                            } elseif ($lastMsg && !$lastMsg->created_at?->isToday()) {
                                $timeStr = $lastMsg->created_at?->format('M d');
                            }
                            
                            $subStr = $lastMsg ? Str::limit(strip_tags($lastMsg->message), 30) : __('admin.chat.members', ['count' => $c->users_count]);
                        @endphp
                        <a href="{{ route('admin.opportunity_chats.show', $c->id) }}" 
                           class="conversation-item d-flex align-items-center justify-content-between text-decoration-none {{ $isActive ? 'active' : '' }}" 
                           data-type="{{ $c->type ?? 'group' }}">
                            <div class="conversation-left">
                                <div class="conversation-icon-container">
                                    <span class="material-symbols-outlined">group</span>
                                </div>
                                <div class="conversation-details">
                                    <h6 class="conversation-name text-truncate">{{ $c->chatable?->title ?? 'Chat' }}</h6>
                                    <p class="conversation-preview text-truncate">{{ $subStr }}</p>
                                </div>
                            </div>
                            <div class="conversation-right">
                                <span class="conversation-meta-time">{{ $timeStr }}</span>
                                @if (isset($c->unread_count) && $c->unread_count > 0)
                                    <span class="conversation-unread-badge">{{ $c->unread_count }}</span>
                                @endif
                            </div>
                        </a>
                    @empty
                        <div class="text-center text-muted small py-4">{{ __('admin.chat.no_conversations') }}</div>
                    @endforelse
                </div>
            </div>

            {{-- Right Message Panel --}}
            <div class="chat-main-panel">
                <div class="chat-main-header">
                    <div class="chat-main-header-info">
                        <!-- Mobile Toggle Sidebar Button -->
                        <button type="button" class="btn btn-outline-secondary d-lg-none me-2 p-1 d-flex align-items-center justify-content-center" id="mobileShowSidebarBtn" style="border-radius: 8px; width: 36px; height: 36px;">
                            <span class="material-symbols-outlined fs-20">forum</span>
                        </button>
                        
                        <div class="chat-main-header-icon">
                            <span class="material-symbols-outlined">group</span>
                        </div>
                        <div>
                            <h5 class="chat-main-header-title">{{ $chat->chatable?->title ?? 'Chat' }}</h5>
                            <span class="chat-main-header-members">{{ __('admin.chat.members', ['count' => $chat->users_count ?? $chat->users()->count()]) }}</span>
                        </div>
                    </div>
                </div>

                <div class="chat-main-body" id="chatArea">
                    <div id="load-more-trigger" class="text-center py-2 text-muted small cursor-pointer">
                        <div class="spinner-border spinner-border-sm d-none" role="status"></div>
                        <span class="trigger-text">{{ __('admin.chat.scroll_more') }}</span>
                    </div>
                    
                    <div id="messageContainer" class="d-flex flex-column">
                        @foreach ($messages->reverse() as $m)
                            @php 
                                $isMine = $m->user_id === auth()->id(); 
                                $hasText = !empty($m->message);
                                $hasFile = !empty($m->file);
                                $isMedia = false;
                                if ($hasFile) {
                                    $ext = strtolower(pathinfo($m->file, PATHINFO_EXTENSION));
                                    $isMedia = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'mp3', 'wav', 'ogg', 'm4a', 'aac', 'webm']);
                                }
                                $isPlain = $isMedia && !$hasText;
                            @endphp
                            <div class="msg-row {{ $isMine ? 'mine' : 'others' }}">
                                @if ($m->user?->avatar)
                                    <img src="{{ asset($m->user->avatar) }}" class="msg-avatar-img" alt="">
                                @else
                                    @php
                                        $initial = strtoupper(mb_substr($m->user?->name ?? 'U', 0, 1));
                                        $colors = ['#0cbab8', '#22c55e', '#8b5cf6', '#f97316', '#64748b'];
                                        $bg = $colors[ord($initial) % count($colors)];
                                    @endphp
                                    <span class="msg-avatar-initial" style="background: {{ $bg }}">{{ $initial }}</span>
                                @endif
                                <div class="msg-content-wrapper">
                                    @if (!$isMine)
                                        <span class="msg-sender-name">{{ $m->user?->name ?? 'User' }}</span>
                                    @endif
                                    <div class="msg-bubble {{ $isPlain ? 'bg-transparent shadow-none p-0 border-0' : '' }}">
                                        @if ($m->message)
                                            <div class="message-text">{{ $m->message }}</div>
                                        @endif
                                        @if ($m->file)
                                            <div class="message-file {{ $m->message ? 'mt-2' : '' }}">
                                                @php 
                                                    $ext = pathinfo($m->file, PATHINFO_EXTENSION);
                                                    $audioExts = ['mp3', 'wav', 'ogg', 'm4a', 'aac', 'webm'];
                                                @endphp
                                                @if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                                    <a href="{{ asset($m->file) }}" target="_blank" class="d-block overflow-hidden rounded-3">
                                                        <img src="{{ asset($m->file) }}" class="img-fluid" style="max-height: 250px; width: 100%; object-fit: cover;" alt="file">
                                                    </a>
                                                @elseif (in_array(strtolower($ext), $audioExts))
                                                    <audio controls class="mw-100 mt-1" style="height: 35px;">
                                                        <source src="{{ asset($m->file) }}" type="audio/{{ $ext == 'm4a' ? 'mp4' : strtolower($ext) }}">
                                                        Your browser does not support the audio element.
                                                    </audio>
                                                @else
                                                    <a href="{{ asset($m->file) }}" target="_blank" class="d-flex align-items-center gap-2 text-decoration-none py-2 px-3 rounded-3 {{ $isMine ? 'bg-white bg-opacity-10 text-white' : 'bg-light text-primary' }}">
                                                        <i class="material-symbols-outlined">description</i>
                                                        <div class="overflow-hidden">
                                                            <div class="text-truncate small fw-medium" style="max-width: 150px;">{{ basename($m->file) }}</div>
                                                            <div class="smaller opacity-75 text-uppercase">{{ $ext }}</div>
                                                        </div>
                                                    </a>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                    <span class="msg-timestamp">{{ $m->created_at?->format('h:i A') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="chat-main-footer">
                    <form id="chatForm" action="{{ route('admin.opportunity_chats.messages.store', $chat->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="chat-input-container">
                            <label class="chat-action-btn m-0" for="fileInput">
                                <span class="material-symbols-outlined">attach_file</span>
                            </label>
                            <input type="file" name="file" id="fileInput" class="d-none">
                            
                            <button type="button" id="voiceRecBtn" class="chat-action-btn">
                                <span class="material-symbols-outlined">mic</span>
                            </button>

                            <textarea name="message" rows="1" placeholder="{{ __('admin.chat.type_message') }}" style="height: 38px;"></textarea>
                            
                            <button type="submit" id="submitBtn" class="chat-send-btn">
                                <span class="material-symbols-outlined">send</span>
                            </button>
                        </div>

                        <!-- Voice Recording UI -->
                        <div id="voiceRecUI" class="d-none bg-light p-2 rounded-3 mt-2">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="recording-indicator">
                                    <div class="recording-pulse"></div>
                                    <span id="voiceTimer">00:00</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <button type="button" id="cancelRecBtn" class="btn btn-sm btn-outline-danger border-0">
                                        <i class="material-symbols-outlined fs-18">close</i>
                                    </button>
                                    <button type="button" id="stopRecBtn" class="btn btn-sm btn-primary rounded-circle p-1">
                                        <i class="material-symbols-outlined fs-20">stop</i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div id="file-preview" class="mt-2 small text-muted d-none">
                            <span class="file-name"></span>
                            <button type="button" class="btn btn-link btn-sm text-danger p-0 ms-2" onclick="clearFile()">Remove</button>
                        </div>
                    </form>
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

            // Mobile Sidebar Toggle
            const mobileShowSidebarBtn = document.getElementById('mobileShowSidebarBtn');
            const mobileCloseSidebarBtn = document.getElementById('mobileCloseSidebarBtn');
            const chatContainerLayout = document.querySelector('.chat-container-layout');

            if (mobileShowSidebarBtn && chatContainerLayout) {
                mobileShowSidebarBtn.addEventListener('click', function() {
                    chatContainerLayout.classList.add('sidebar-visible');
                });
            }

            if (mobileCloseSidebarBtn && chatContainerLayout) {
                mobileCloseSidebarBtn.addEventListener('click', function() {
                    chatContainerLayout.classList.remove('sidebar-visible');
                });
            }

            chatArea.addEventListener('scroll', function() {
                if (chatArea.scrollTop === 0 && !isLoading && currentPage < lastPage) {
                    loadMoreMessages();
                }
            });

            // Client-side search and tabs filter
            const searchInput = document.getElementById('chatSearchInput');
            const tabBtns = document.querySelectorAll('.chat-tab-btn');
            const chatItems = document.querySelectorAll('.conversation-item');

            function filterChats() {
                const query = searchInput.value.toLowerCase().trim();
                const activeTab = document.querySelector('.chat-tab-btn.active').dataset.type; // 'all', 'single', 'group'

                chatItems.forEach(item => {
                    const name = item.querySelector('.conversation-name').innerText.toLowerCase();
                    const preview = item.querySelector('.conversation-preview').innerText.toLowerCase();
                    const type = item.dataset.type; // 'single', 'group'

                    const matchesSearch = name.includes(query) || preview.includes(query);
                    const matchesTab = activeTab === 'all' || type === activeTab;

                    if (matchesSearch && matchesTab) {
                        item.style.setProperty('display', 'flex', 'important');
                    } else {
                        item.style.setProperty('display', 'none', 'important');
                    }
                });
            }

            if (searchInput) {
                searchInput.addEventListener('input', filterChats);
            }

            tabBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    tabBtns.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    filterChats();
                });
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
                
                // Format timestamp
                const dateObj = new Date(m.created_at);
                let hours = dateObj.getHours();
                let minutes = dateObj.getMinutes();
                const ampm = hours >= 12 ? 'PM' : 'AM';
                hours = hours % 12;
                hours = hours ? hours : 12;
                minutes = minutes < 10 ? '0'+minutes : minutes;
                const timeStr = hours + ':' + minutes + ' ' + ampm;

                let fileHtml = '';
                let isMedia = false;
                if (m.file) {
                    const fileUrl = m.file.startsWith('http') ? m.file : '/' + m.file;
                    const ext = m.file.split('.').pop().toLowerCase();
                    const audioExts = ['mp3', 'wav', 'ogg', 'm4a', 'aac', 'webm'];
                    const imgExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                    if (imgExts.includes(ext)) {
                        isMedia = true;
                        fileHtml = `<div class="message-file ${m.message ? 'mt-2' : ''}">
                            <a href="${fileUrl}" target="_blank" class="d-block overflow-hidden rounded-3">
                                <img src="${fileUrl}" class="img-fluid" style="max-height: 250px; width: 100%; object-fit: cover;" alt="file">
                            </a>
                        </div>`;
                    } else if (audioExts.includes(ext)) {
                        isMedia = true;
                        const type = ext === 'm4a' ? 'audio/mp4' : `audio/${ext}`;
                        fileHtml = `<div class="message-file ${m.message ? 'mt-2' : ''}">
                            <audio controls class="mw-100 mt-1" style="height: 35px;">
                                <source src="${fileUrl}" type="${type}">
                                Your browser does not support the audio element.
                            </audio>
                        </div>`;
                    } else {
                        fileHtml = `<div class="message-file ${m.message ? 'mt-2' : ''}">
                            <a href="${fileUrl}" target="_blank" class="d-flex align-items-center gap-2 text-decoration-none py-2 px-3 rounded-3 ${isMine ? 'bg-white bg-opacity-10 text-white' : 'bg-light text-primary'}">
                                <i class="material-symbols-outlined">description</i>
                                <div class="overflow-hidden">
                                    <div class="text-truncate small fw-medium" style="max-width: 150px;">${m.file.split('/').pop()}</div>
                                    <div class="smaller opacity-75 text-uppercase">${ext}</div>
                                </div>
                            </a>
                        </div>`;
                    }
                }

                const isPlain = isMedia && !m.message;
                
                let avatarHtml = '';
                if (m.user?.avatar) {
                    avatarHtml = `<img src="${avatar}" class="msg-avatar-img" alt="">`;
                } else {
                    const initial = name.charAt(0).toUpperCase();
                    const colors = ['#0cbab8', '#22c55e', '#8b5cf6', '#f97316', '#64748b'];
                    const bg = colors[name.charCodeAt(0) % colors.length];
                    avatarHtml = `<span class="msg-avatar-initial" style="background: ${bg}">${initial}</span>`;
                }
                
                return `
                    <div class="msg-row ${isMine ? 'mine' : 'others'}">
                        ${avatarHtml}
                        <div class="msg-content-wrapper">
                            ${!isMine ? `<span class="msg-sender-name">${name}</span>` : ''}
                            <div class="msg-bubble ${isPlain ? 'bg-transparent shadow-none p-0 border-0' : ''}">
                                ${m.message ? `<div class="message-text">${m.message}</div>` : ''}
                                ${fileHtml}
                            </div>
                            <span class="msg-timestamp">${timeStr}</span>
                        </div>
                    </div>
                `;
            }
            
            // Echo Listener for real-time updates
            if (window.Echo) {
                window.Echo.private('opportunity_chat.{{ $chat->id }}')
                    .listen('.opportunity.chat', (e) => {
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
            
            // Voice Recording Variables
            const voiceRecBtn = document.getElementById('voiceRecBtn');
            const voiceRecUI = document.getElementById('voiceRecUI');
            const voiceTimer = document.getElementById('voiceTimer');
            const stopRecBtn = document.getElementById('stopRecBtn');
            const cancelRecBtn = document.getElementById('cancelRecBtn');
            const submitBtn = document.getElementById('submitBtn');
            
            let mediaRecorder;
            let audioChunks = [];
            let timerInterval;
            let startTime;
            let recordedBlob = null;

            if (fileInput) {
                fileInput.addEventListener('change', function() {
                    if (this.files && this.files[0]) {
                        fileNameSpan.textContent = this.files[0].name;
                        filePreview.classList.remove('d-none');
                        recordedBlob = null;
                    } else {
                        clearFile();
                    }
                });
            }

            window.clearFile = function() {
                fileInput.value = '';
                filePreview.classList.add('d-none');
                fileNameSpan.textContent = '';
                recordedBlob = null;
            };

            // Voice Recording Logic
            if (voiceRecBtn) {
                voiceRecBtn.addEventListener('click', async function() {
                    try {
                        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                        mediaRecorder = new MediaRecorder(stream);
                        audioChunks = [];

                        mediaRecorder.ondataavailable = (event) => {
                            audioChunks.push(event.data);
                        };

                        mediaRecorder.onstop = () => {
                            recordedBlob = new Blob(audioChunks, { type: 'audio/webm' });
                            fileNameSpan.textContent = "Voice Message Recorded";
                            filePreview.classList.remove('d-none');
                            stream.getTracks().forEach(track => track.stop());
                        };

                        mediaRecorder.start();
                        showVoiceUI();
                    } catch (err) {
                        console.error("Microphone access denied:", err);
                        alert("Could not access microphone.");
                    }
                });
            }

            function showVoiceUI() {
                voiceRecUI.classList.remove('d-none');
                voiceRecBtn.classList.add('d-none');
                startTime = Date.now();
                updateTimer();
                timerInterval = setInterval(updateTimer, 1000);
            }

            function hideVoiceUI() {
                voiceRecUI.classList.add('d-none');
                voiceRecBtn.classList.remove('d-none');
                clearInterval(timerInterval);
            }

            function updateTimer() {
                const elapsed = Math.floor((Date.now() - startTime) / 1000);
                const mins = String(Math.floor(elapsed / 60)).padStart(2, '0');
                const secs = String(elapsed % 60).padStart(2, '0');
                voiceTimer.textContent = `${mins}:${secs}`;
            }

            if (stopRecBtn) {
                stopRecBtn.addEventListener('click', function() {
                    if (mediaRecorder && mediaRecorder.state === 'recording') {
                        mediaRecorder.stop();
                        hideVoiceUI();
                    }
                });
            }

            if (cancelRecBtn) {
                cancelRecBtn.addEventListener('click', function() {
                    if (mediaRecorder && mediaRecorder.state === 'recording') {
                        mediaRecorder.stop();
                    }
                    hideVoiceUI();
                    recordedBlob = null;
                    fileNameSpan.textContent = '';
                    filePreview.classList.add('d-none');
                });
            }

            if (chatForm) {
                chatForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(chatForm);
                    const textarea = chatForm.querySelector('textarea[name="message"]');
                    
                    if (recordedBlob) {
                        formData.append('file', recordedBlob, 'voice_message.webm');
                    }
                    
                    if (!textarea.value.trim() && !fileInput.files.length && !recordedBlob) return;
                    
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

            document.addEventListener('play', function(e) {
                const audios = document.getElementsByTagName('audio');
                for (let i = 0, len = audios.length; i < len; i++) {
                    if (audios[i] != e.target) {
                        audios[i].pause();
                    }
                }
            }, true);
        });
    </script>
@endpush
