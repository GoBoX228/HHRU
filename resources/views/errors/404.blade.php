<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 | Za_raboty</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
</head>
<body>
@php
    $currentUser = auth()->user();
@endphp
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
            </nav>

            <div class="user-menu">
                @if($currentUser)
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-medium">{{ $currentUser->name }}</span>
                        <span class="badge badge-neutral">{{ $currentUser->role }}</span>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="nav-link">Войти</a>
                    <a href="{{ route('register') }}" class="btn btn-primary">Регистрация</a>
                @endif
            </div>
        </div>
    </header>

    <main class="main-content container">
        <section class="error-shell">
            <div class="error-card">
                <div class="error-code">404</div>
                <h1 class="error-title">Страница не найдена</h1>
                <p class="error-text">
                    Похоже, ссылка устарела или страница была перемещена. Вернитесь на главную и продолжите работу с платформой.
                </p>

                <div class="error-actions">
                    <a href="{{ route('home') }}" class="btn btn-primary">На главную</a>

                    @if($currentUser && ($currentUser->role ?? '') === 'freelancer')
                        <a href="{{ route('profile') }}" class="btn btn-outline">В профиль</a>
                    @elseif($currentUser)
                        <a href="{{ route('home') }}" class="btn btn-outline">На главную</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline">Войти</a>
                    @endif

                    <a href="javascript:history.back()" class="btn btn-outline">Назад</a>
                </div>
            </div>
        </section>
    </main>
</div>
</body>
</html>
