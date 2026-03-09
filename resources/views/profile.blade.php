<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль | Za_raboty</title>
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
            </div>
        </div>
    </header>

    <main class="main-content container"></main>
</div>
</body>
</html>
