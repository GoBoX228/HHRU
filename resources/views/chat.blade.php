@extends('layouts.app')
@section('title', 'Chat')
@section('content')
@if(! $canAccess)
    <div class="text-center text-muted access-state">Доступ запрещен.</div>
@elseif($notFound)
    <div class="text-center access-state">
        <h2 class="page-title">Чат не найден</h2>
        <a href="{{ route('chats') }}" class="btn btn-outline mt-4">Вернуться к чатам</a>
    </div>
@else
    <div class="content-narrow">
        <div class="chat-box">
            <div class="chat-header">
                <a href="{{ route('chats') }}" class="btn-icon chat-back-btn" title="Назад">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M15 6L9 12L15 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </a>

                <div class="flex items-center gap-3">
                    <div class="avatar chat-user-avatar">
                        {{ mb_substr((string) ($otherUser?->name ?? ''), 0, 1) }}
                    </div>
                    <div>
                        <h2 class="chat-header-name">{{ $otherUser?->name }}</h2>
                        <p class="text-sm text-muted chat-header-vacancy">{{ $vacancy?->title }}</p>
                    </div>
                </div>
            </div>

            <div
                class="chat-messages"
                id="chat-messages"
                data-messages-url="{{ route('chat.messages.index', ['chat' => $chat->id]) }}"
                data-current-user-id="{{ $currentUser->id }}"
            >
                @if($chatMessages->isEmpty())
                    <div class="flex flex-col items-center justify-center h-full text-muted" data-chat-empty>
                        <p class="mb-2">Пока нет сообщений.</p>
                        <p class="text-sm">Начните диалог!</p>
                    </div>
                @else
                    @foreach($chatMessages as $message)
                        @php
                            $isMe = (int) $message->sender_user_id === (int) $currentUser->id;
                        @endphp
                        <div class="flex {{ $isMe ? 'justify-end' : '' }}" data-message-id="{{ $message->id }}">
                            <div class="message {{ $isMe ? 'me' : 'other' }}">
                                <p class="message-text">{{ $message->text }}</p>
                                <p class="message-time">{{ $message->created_at?->format('H:i') }}</p>
                            </div>
                        </div>
                    @endforeach
                @endif
                <div id="messages-end"></div>
            </div>

            <div class="chat-input">
                <form method="POST" action="{{ route('chat.messages.store', ['chat' => $chat->id]) }}" class="flex gap-2 w-full" id="chat-form">
                    @csrf
                    <input
                        type="text"
                        name="text"
                        value="{{ old('text') }}"
                        placeholder="Введите сообщение..."
                        class="form-control chat-message-input"
                        id="message-input"
                        required
                    >
                    <button type="submit" class="btn btn-primary chat-send-btn" id="send-button">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M22 2L11 13" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                            <path d="M22 2L15 22L11 13L2 9L22 2Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"></path>
                        </svg>
                    </button>
                </form>

                @if(session('chat_error'))
                    <div class="alert alert-danger mt-2">{{ session('chat_error') }}</div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger mt-2">{{ $errors->first('text') }}</div>
                @endif
            </div>
        </div>
    </div>
@endif
@endsection