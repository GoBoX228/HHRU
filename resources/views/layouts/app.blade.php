<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'IT-проекты')</title>
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
                        <path d="M9 7V5C9 3.9 9.9 3 11 3H13C14.1 3 15 3.9 15 5V7" stroke="currentColor" stroke-width="2"></path>
                    </svg>
                </div>
                IT-проекты
            </a>

            <nav class="nav">
                <a href="{{ route('home') }}" class="nav-link">Вакансии</a>
                <a href="{{ route('employer.dashboard') }}" class="nav-link">Кабинет работодателя</a>
            </nav>

            <div class="user-menu">
                @if($currentUser)
                    <span class="nav-link">{{ $currentUser['name'] }} ({{ $currentUser['role'] }})</span>
                @else
                    <span class="nav-link">Гость</span>
                @endif

                <form action="{{ route('demo.user.switch') }}" method="POST" class="demo-user-switch">
                    @csrf
                    <select name="user_id" class="form-control form-control-sm" onchange="this.form.submit()">
                        <option value="guest" @selected(!$currentUser)>Гость</option>
                        <option value="3" @selected($currentUser && $currentUser['id'] === '3')>Фрилансер</option>
                        <option value="2" @selected($currentUser && $currentUser['id'] === '2')>Работодатель</option>
                        <option value="1" @selected($currentUser && $currentUser['id'] === '1')>Админ</option>
                    </select>
                    <noscript>
                        <button type="submit" class="btn btn-outline">Применить</button>
                    </noscript>
                </form>
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
</body>
</html>
