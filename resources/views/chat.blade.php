<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Чат | Za_raboty</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
</head>
<body>
<div class="app-wrapper">
    <header class="header">
        <div class="container header-content">
            <a href="{{ route('home') }}" class="logo">
                <div class="logo-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M3 7H21V20H3V7Z" stroke="currentColor" stroke-width="2"></path>
                        <path d="M9 7V5C9 3.89543 9.89543 3 11 3H13C14.1046 3 15 3.89543 15 5V7" stroke="currentColor" stroke-width="2"></path>
                    </svg>
                </div>
                IT-Проекты
            </a>

            <nav class="nav">
                <a href="{{ route('home') }}" class="nav-link">Вакансии</a>

                @if(($currentUser?->role ?? '') === 'freelancer')
                    <a href="#" class="nav-link">Мои отклики</a>
                    <a href="{{ route('chats') }}" class="nav-link">Чаты</a>
                @elseif(($currentUser?->role ?? '') === 'employer')
                    <a href="#" class="nav-link">Мои вакансии</a>
                    <a href="{{ route('chats') }}" class="nav-link">Чаты</a>
                @elseif(($currentUser?->role ?? '') === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="nav-link">Панель админа</a>
                @endif
            </nav>

            <div class="user-menu">
                @if($currentUser)
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-medium">{{ $currentUser->name }}</span>
                        <span class="badge badge-neutral">{{ $currentUser->role }}</span>
                    </div>

                    @if(($currentUser?->role ?? '') === 'freelancer')
                        <a href="{{ route('profile') }}" class="btn-icon" title="Мой профиль">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="2"></circle>
                                <path d="M4 20C4 16.6863 7.58172 14 12 14C16.4183 14 20 16.6863 20 20" stroke="currentColor" stroke-width="2"></path>
                            </svg>
                        </a>
                    @endif

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn-icon" title="Выйти">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M9 21H5C3.89543 21 3 20.1046 3 19V5C3 3.89543 3.89543 3 5 3H9" stroke="currentColor" stroke-width="2"></path>
                                <path d="M16 17L21 12L16 7" stroke="currentColor" stroke-width="2"></path>
                                <path d="M21 12H9" stroke="currentColor" stroke-width="2"></path>
                            </svg>
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="nav-link">Войти</a>
                    <a href="{{ route('register') }}" class="btn btn-primary">Регистрация</a>
                @endif
            </div>
        </div>
    </header>

    <main class="main-content container">
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

                    <div class="chat-messages" id="chat-messages">
                        @if($chatMessages->isEmpty())
                            <div class="flex flex-col items-center justify-center h-full text-muted">
                                <p class="mb-2">Пока нет сообщений.</p>
                                <p class="text-sm">Начните диалог!</p>
                            </div>
                        @else
                            @foreach($chatMessages as $message)
                                @php
                                    $isMe = (int) $message->sender_user_id === (int) $currentUser->id;
                                @endphp
                                <div class="flex {{ $isMe ? 'justify-end' : '' }}">
                                    <div class="message {{ $isMe ? 'me' : 'other' }}">
                                        <p class="message-text">{{ $message->text }}</p>
                                        <p class="message-time">{{ $message->created_at?->diffForHumans() }}</p>
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
    </main>
</div>

<script>
    (() => {
        const messagesEnd = document.getElementById('messages-end');
        const input = document.getElementById('message-input');
        const sendButton = document.getElementById('send-button');

        if (messagesEnd) {
            messagesEnd.scrollIntoView({ behavior: 'auto' });
        }

        if (!input || !sendButton) {
            return;
        }

        const updateState = () => {
            const hasText = input.value.trim().length > 0;
            sendButton.disabled = !hasText;
            sendButton.style.opacity = hasText ? '1' : '0.5';
        };

        input.addEventListener('input', updateState);
        updateState();
    })();
</script>
</body>
</html>
