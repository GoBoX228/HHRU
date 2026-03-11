<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мои отклики | Za_raboty</title>
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
                    <a href="{{ route('applications') }}" class="nav-link">Мои отклики</a>
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

    <main class="main-content container">
        @if(! $canAccess)
            <div class="text-center text-muted access-state">Доступ запрещен. Только для фрилансеров.</div>
        @else
            <div class="mb-8">
                <h1 class="page-title">Мои отклики</h1>
                <p class="page-subtitle">Отслеживайте статус ваших откликов.</p>
            </div>

            <div class="flex flex-col gap-4">
                @if($myApplications->isEmpty())
                    <div class="card text-center empty-card">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" class="empty-icon" aria-hidden="true">
                            <path d="M3 7H21V20H3V7Z" stroke="currentColor" stroke-width="2"></path>
                            <path d="M9 7V5C9 3.89543 9.89543 3 11 3H13C14.1046 3 15 3.89543 15 5V7" stroke="currentColor" stroke-width="2"></path>
                        </svg>
                        <h3 class="section-title">Пока нет откликов</h3>
                        <p class="text-muted">Начните откликаться на вакансии, чтобы увидеть их здесь.</p>
                        <a href="{{ route('home') }}" class="btn btn-primary mt-4">Смотреть вакансии</a>
                    </div>
                @else
                    @foreach($myApplications as $application)
                        @php
                            $vacancy = $application->vacancy;
                            $employer = $vacancy?->employerProfile;
                            $chat = $vacancy?->chats->first();
                            $statusClass = match($application->status) {
                                'pending' => 'badge-warning',
                                'accepted' => 'badge-success',
                                'rejected' => 'badge-danger',
                                default => 'badge-neutral',
                            };
                            $statusLabel = match($application->status) {
                                'pending' => 'На рассмотрении',
                                'accepted' => 'Принят',
                                'rejected' => 'Отклонен',
                                'canceled' => 'Отменен',
                                default => ucfirst((string) $application->status),
                            };
                        @endphp

                        @if($vacancy && $employer)
                            <div class="card card-hover application-card">
                                <div class="flex justify-between items-start application-row">
                                    <div class="application-main">
                                        <a href="{{ route('home') }}" class="text-xl application-link">{{ $vacancy->title }}</a>
                                        <div class="flex items-center gap-4 mt-2 text-sm text-muted application-meta">
                                            <span class="flex items-center gap-2 company-name">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <path d="M4 21V7L12 3L20 7V21" stroke="currentColor" stroke-width="2"></path>
                                                    <path d="M9 21V11H15V21" stroke="currentColor" stroke-width="2"></path>
                                                </svg>
                                                {{ $employer->company_name }}
                                            </span>
                                            <span class="flex items-center gap-2">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"></circle>
                                                    <path d="M12 7V12L15 14" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                                                </svg>
                                                Отправлен {{ $application->created_at?->diffForHumans() }}
                                            </span>
                                        </div>

                                        <div class="application-letter mt-4">
                                            <h4 class="text-sm application-letter-title">Ваше сопроводительное письмо</h4>
                                            <p class="text-sm text-muted application-letter-text">{{ $application->cover_letter ?: 'Сопроводительное письмо не добавлено.' }}</p>
                                        </div>
                                    </div>

                                    <div class="flex flex-col items-end gap-4 application-side">
                                        <div class="badge {{ $statusClass }} application-status-badge">
                                            @if($application->status === 'pending')
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"></circle>
                                                    <path d="M12 7V12L15 14" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                                                </svg>
                                            @elseif($application->status === 'accepted')
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <path d="M20 7L9 18L4 13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                                </svg>
                                            @elseif($application->status === 'rejected')
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <path d="M7 7L17 17" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                                                    <path d="M17 7L7 17" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                                                </svg>
                                            @endif
                                            {{ $statusLabel }}
                                        </div>

                                        @if($application->status === 'accepted' && $chat)
                                            <a href="{{ route('chat.show', ['chat' => $chat->id]) }}" class="btn btn-outline w-full text-center">Перейти в чат</a>
                                        @elseif($application->status === 'accepted')
                                            <a href="{{ route('chats') }}" class="btn btn-outline w-full text-center">Перейти в чат</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                @endif
            </div>
        @endif
    </main>
</div>
</body>
</html>
