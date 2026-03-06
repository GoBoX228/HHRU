<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация | Za_raboty</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
</head>
<body>
@php
    $selectedRole = old('role', 'freelancer');
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
                <a href="{{ route('login') }}" class="nav-link">Войти</a>
                <a href="{{ route('register') }}" class="btn btn-primary">Регистрация</a>
            </div>
        </div>
    </header>

    <main class="main-content container">
        <div class="auth-container">
            <div class="auth-card auth-card-wide">
                <div class="text-center mb-8">
                    <h2 class="page-title" style="font-size: 24px;">Создать аккаунт</h2>
                    <p class="text-sm text-muted">
                        Уже есть аккаунт?
                        <a href="{{ route('login') }}" style="color: var(--primary); font-weight: 500;">Войти</a>
                    </p>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div class="tabs">
                    <button type="button" class="tab w-full {{ $selectedRole === 'freelancer' ? 'active' : '' }}" data-role="freelancer">
                        Я Фрилансер
                    </button>
                    <button type="button" class="tab w-full {{ $selectedRole === 'employer' ? 'active' : '' }}" data-role="employer">
                        Я Работодатель
                    </button>
                </div>

                <form method="POST" action="{{ route('register.submit') }}">
                    @csrf
                    <input id="role" type="hidden" name="role" value="{{ $selectedRole }}">

                    <div class="form-group">
                        <label for="name" class="form-label">ФИО</label>
                        <div class="input-wrap">
                            <svg class="input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="2"></circle>
                                <path d="M4 20C4 16.6863 7.58172 14 12 14C16.4183 14 20 16.6863 20 20" stroke="currentColor" stroke-width="2"></path>
                            </svg>
                            <input
                                id="name"
                                name="name"
                                type="text"
                                required
                                value="{{ old('name') }}"
                                class="form-control input-with-icon"
                                placeholder="Иван Иванов"
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Email адрес</label>
                        <div class="input-wrap">
                            <svg class="input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M4 6H20V18H4V6Z" stroke="currentColor" stroke-width="2"></path>
                                <path d="M4 7L12 13L20 7" stroke="currentColor" stroke-width="2"></path>
                            </svg>
                            <input
                                id="email"
                                name="email"
                                type="email"
                                required
                                value="{{ old('email') }}"
                                class="form-control input-with-icon"
                                placeholder="you@example.com"
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="phone" class="form-label">Номер телефона</label>
                        <div class="input-wrap">
                            <svg class="input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M5 4H9L11 9L8.5 10.5C9.70434 12.9546 11.6297 14.8801 14.0845 16.0845L15.5 13.5L20.5 15.5V19C20.5 19.5304 20.2893 20.0391 19.9142 20.4142C19.5391 20.7893 19.0304 21 18.5 21C14.2565 20.7427 10.2549 18.9419 7.25736 15.9444C4.25984 12.9469 2.45902 8.94535 2.20166 4.70184C2.20166 4.17141 2.41238 3.6627 2.78745 3.28762C3.16252 2.91255 3.67123 2.70184 4.20166 2.70184H5V4Z" stroke="currentColor" stroke-width="1.5"></path>
                            </svg>
                            <input
                                id="phone"
                                name="phone"
                                type="tel"
                                required
                                value="{{ old('phone') }}"
                                class="form-control input-with-icon"
                                placeholder="+7 (999) 000-00-00"
                            >
                        </div>
                    </div>

                    <div id="company-field" class="form-group {{ $selectedRole === 'employer' ? '' : 'hidden' }}">
                        <label for="company_name" class="form-label">Название компании</label>
                        <div class="input-wrap">
                            <svg class="input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M3 21H21" stroke="currentColor" stroke-width="2"></path>
                                <rect x="5" y="7" width="14" height="14" stroke="currentColor" stroke-width="2"></rect>
                                <path d="M9 7V3H15V7" stroke="currentColor" stroke-width="2"></path>
                            </svg>
                            <input
                                id="company_name"
                                name="company_name"
                                type="text"
                                value="{{ old('company_name') }}"
                                class="form-control input-with-icon"
                                placeholder="ООО Ромашка"
                            >
                        </div>
                    </div>

                    <div class="form-group mb-8">
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
                                placeholder="••••••••"
                            >
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-full">Зарегистрироваться</button>
                </form>
            </div>
        </div>
    </main>
</div>
<script>
    (() => {
        const tabs = document.querySelectorAll('[data-role]');
        const roleInput = document.getElementById('role');
        const companyField = document.getElementById('company-field');
        const companyInput = document.getElementById('company_name');

        const setRole = (role) => {
            roleInput.value = role;

            tabs.forEach((tab) => {
                tab.classList.toggle('active', tab.dataset.role === role);
            });

            const isEmployer = role === 'employer';
            companyField.classList.toggle('hidden', !isEmployer);
            companyInput.toggleAttribute('required', isEmployer);
        };

        tabs.forEach((tab) => {
            tab.addEventListener('click', () => setRole(tab.dataset.role));
        });

        setRole(roleInput.value || 'freelancer');
    })();
</script>
</body>
</html>
