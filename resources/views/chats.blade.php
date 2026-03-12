@extends('layouts.app')
@section('title', 'Chats')
@section('content')
@if(! $canAccess)
            <div class="text-center text-muted access-state">Доступ запрещен.</div>
        @else
            <div class="content-narrow">
                <div class="mb-8">
                    <h1 class="page-title">Сообщения</h1>
                    <p class="page-subtitle">Общайтесь с партнерами по проекту.</p>
                </div>

                <div class="flex flex-col gap-4">
                    @if($myChats->isEmpty())
                        <div class="card text-center empty-card">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" class="empty-icon">
                                <path d="M4 4H20V16H5.2L4 17.2V4Z" stroke="currentColor" stroke-width="2"></path>
                                <path d="M8 9H16" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                                <path d="M8 13H13" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                            </svg>
                            <h3 class="section-title">Нет активных чатов</h3>
                            <p class="text-muted">Чаты появятся здесь, когда отклик будет принят.</p>
                        </div>
                    @else
                        @foreach($myChats as $chat)
                            @php
                                $otherInitial = mb_substr((string) $chat['other_user_name'], 0, 1);
                            @endphp
                            <a
                                href="{{ route('chat.show', ['chat' => $chat['id']]) }}"
                                class="card card-hover flex items-center gap-6 chat-list-item"
                            >
                                <div class="avatar chat-avatar">{{ $otherInitial }}</div>

                                <div class="chat-list-content">
                                    <div class="flex justify-between items-start mb-2 gap-2">
                                        <h2 class="chat-user-name">{{ $chat['other_user_name'] }}</h2>
                                        @if($chat['last_message_at'])
                                            <span class="text-sm text-muted chat-time">{{ $chat['last_message_at']->diffForHumans() }}</span>
                                        @endif
                                    </div>

                                    <div class="flex items-center gap-2 text-sm text-muted mb-2">
                                        <span class="chat-vacancy-title">{{ $chat['vacancy_title'] }}</span>
                                    </div>

                                    <p class="text-sm text-muted text-truncate">
                                        @if($chat['last_message_text'])
                                            <span class="{{ $chat['is_last_message_mine'] ? '' : 'font-medium chat-last-message-other' }}">
                                                {{ $chat['is_last_message_mine'] ? 'Вы: ' : '' }}{{ $chat['last_message_text'] }}
                                            </span>
                                        @else
                                            <span class="chat-empty-hint">Пока нет сообщений. Начните диалог!</span>
                                        @endif
                                    </p>
                                </div>
                            </a>
                        @endforeach
                    @endif
                </div>
            </div>
        @endif
@endsection
