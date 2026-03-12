<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'IT-Проекты')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
</head>
<body>
@php($currentUser = $currentUser ?? auth()->user())
<div class="app-wrapper">
    <header class="header">
        <div class="container header-content">
            <a href="{{ route('home') }}" class="logo">
                <div class="logo-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M3 7H21V20H3V7Z" stroke="currentColor" stroke-width="2"></path>
                        <path d="M9 7V5C9 3.9 9.9 3 11 3H13C14.1 3 15 3.9 15 5V7" stroke="currentColor" stroke-width="2"></path>
                    </svg>
                </div>
                IT-Проекты
            </a>

            <nav class="nav">
                <a href="{{ route('home') }}" class="nav-link">Вакансии</a>

                @if(($currentUser?->role ?? '') === 'freelancer')
                    <a href="{{ route('applications') }}" class="nav-link">Мои отклики</a>
                @endif

                @if(($currentUser?->role ?? '') === 'employer')
                    <a href="{{ route('employer.dashboard') }}" class="nav-link">Кабинет работодателя</a>
                @endif

                @if(in_array($currentUser?->role ?? '', ['freelancer', 'employer'], true))
                    <a href="{{ route('chats') }}" class="nav-link">Чаты</a>
                @endif

                @if(($currentUser?->role ?? '') === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="nav-link">Админ-панель</a>
                @endif
            </nav>

            <div class="user-menu">
                @if($currentUser)
                    <span class="nav-link">{{ $currentUser->name }} ({{ $currentUser->role }})</span>
                    @if(($currentUser?->role ?? '') === 'freelancer')
                        <a href="{{ route('profile') }}" class="btn btn-outline">Профиль</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline">Выйти</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline">Войти</a>
                    <a href="{{ route('register') }}" class="btn btn-primary">Регистрация</a>
                @endif
            </div>
        </div>
    </header>

    <main class="main-content container">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        @yield('content')
    </main>
</div>
<script src="{{ asset('js/app.js') }}" defer></script>
</body>
</html>