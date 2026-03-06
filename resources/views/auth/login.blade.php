<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход | Za_raboty</title>
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
            </nav>

            <div class="user-menu">
                <a href="{{ route('login') }}" class="nav-link">Войти</a>
                <a href="{{ route('register') }}" class="btn btn-primary">Регистрация</a>
            </div>
        </div>
    </header>

    <main class="main-content container">
        <div class="auth-container">
            <div class="auth-card">
                <div class="text-center mb-8">
                    <h2 class="page-title" style="font-size: 24px;">Войдите в аккаунт</h2>
                    <p class="text-sm text-muted">
                        Или
                        <a href="{{ route('register') }}" style="color: var(--primary); font-weight: 500;">создайте новый аккаунт</a>
                    </p>
                </div>

                @if(session('auth_error'))
                    <div class="alert alert-danger">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"></circle>
                            <path d="M12 8V12" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                            <circle cx="12" cy="16" r="1" fill="currentColor"></circle>
                        </svg>
                        {{ session('auth_error') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login.submit') }}">
                    @csrf
                    <div class="form-group">
                        <label for="email-address" class="form-label">Email адрес</label>
                        <div class="input-wrap">
                            <svg class="input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M4 6H20V18H4V6Z" stroke="currentColor" stroke-width="2"></path>
                                <path d="M4 7L12 13L20 7" stroke="currentColor" stroke-width="2"></path>
                            </svg>
                            <input
                                id="email-address"
                                name="email"
                                type="email"
                                required
                                value="{{ old('email') }}"
                                class="form-control input-with-icon"
                                placeholder="admin@test.com"
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Пароль</label>
                        <div class="input-wrap">
                            <svg class="input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <rect x="4" y="11" width="16" height="10" rx="2" stroke="currentColor" stroke-width="2"></rect>
                                <path d="M8 11V8C8 5.79086 9.79086 4 12 4C14.2091 4 16 5.79086 16 8V11" stroke="currentColor" stroke-width="2"></path>
                            </svg>
                            <input
                                id="password"
                                name="password"
                                type="password"
                                required
                                class="form-control input-with-icon"
                                placeholder="Любой пароль (mock)"
                            >
                        </div>
                    </div>

                    <div class="flex justify-between items-center mb-8 mt-4">
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="remember-me" name="remember">
                            <label for="remember-me" class="text-sm">Запомнить меня</label>
                        </div>
                        <a href="#" class="text-sm" style="color: var(--primary); font-weight: 500;">Забыли пароль?</a>
                    </div>

                    <button type="submit" class="btn btn-primary w-full">Войти</button>
                </form>
            </div>
        </div>
    </main>
</div>
</body>
</html>
