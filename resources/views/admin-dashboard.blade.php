@extends('layouts.app')
@section('title', 'Admin Dashboard')
@section('content')
@if(! $canAccess)
            <div class="text-center text-muted access-state">Доступ запрещен. Только для администраторов.</div>
        @else
            <div class="mb-8">
                <h1 class="page-title">Панель администратора</h1>
                <p class="page-subtitle">Статистика платформы и инструменты модерации.</p>
            </div>

            <div class="grid grid-cols-4 gap-grid mb-8">
                <div class="card flex items-center gap-4 stat-card">
                    <div class="avatar stat-avatar">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M16 11C17.6569 11 19 9.65685 19 8C19 6.34315 17.6569 5 16 5C14.3431 5 13 6.34315 13 8C13 9.65685 14.3431 11 16 11Z" stroke="currentColor" stroke-width="2"></path>
                            <path d="M8 11C9.65685 11 11 9.65685 11 8C11 6.34315 9.65685 5 8 5C6.34315 5 5 6.34315 5 8C5 9.65685 6.34315 11 8 11Z" stroke="currentColor" stroke-width="2"></path>
                            <path d="M2 20C2 17.7909 3.79086 16 6 16H10C12.2091 16 14 17.7909 14 20" stroke="currentColor" stroke-width="2"></path>
                            <path d="M13 20C13 18.3431 14.3431 17 16 17H18C20.2091 17 22 18.7909 22 21" stroke="currentColor" stroke-width="2"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm text-muted stat-label">Всего пользователей</div>
                        <div class="text-2xl">{{ $stats['total_users'] }}</div>
                    </div>
                </div>

                <div class="card flex items-center gap-4 stat-card">
                    <div class="avatar stat-avatar">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M3 7H21V20H3V7Z" stroke="currentColor" stroke-width="2"></path>
                            <path d="M9 7V5C9 3.89543 9.89543 3 11 3H13C14.1046 3 15 3.89543 15 5V7" stroke="currentColor" stroke-width="2"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm text-muted stat-label">Активные вакансии</div>
                        <div class="text-2xl">{{ $stats['active_vacancies'] }}</div>
                    </div>
                </div>

                <div class="card flex items-center gap-4 stat-card">
                    <div class="avatar stat-avatar">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M5 3H19C20.1046 3 21 3.89543 21 5V19C21 20.1046 20.1046 21 19 21H5C3.89543 21 3 20.1046 3 19V5C3 3.89543 3.89543 3 5 3Z" stroke="currentColor" stroke-width="2"></path>
                            <path d="M7 8H17" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                            <path d="M7 12H17" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                            <path d="M7 16H13" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm text-muted stat-label">Отклики</div>
                        <div class="text-2xl">{{ $stats['total_applications'] }}</div>
                    </div>
                </div>

                <div class="card flex items-center gap-4 stat-card">
                    <div class="avatar stat-avatar">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 3L19 6V11C19 16 15.5 20.5 12 21C8.5 20.5 5 16 5 11V6L12 3Z" stroke="currentColor" stroke-width="2"></path>
                            <path d="M9 12L11 14L15 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm text-muted stat-label">Заблокированные</div>
                        <div class="text-2xl">{{ $stats['blocked_users'] }}</div>
                    </div>
                </div>
            </div>

            <div class="card admin-card">
                <div class="tabs admin-tabs">
                    <a
                        href="{{ route('admin.dashboard', ['tab' => 'users']) }}"
                        class="tab w-full {{ $activeTab === 'users' ? 'active' : '' }}"
                    >
                        Управление пользователями
                    </a>
                    <a
                        href="{{ route('admin.dashboard', ['tab' => 'vacancies']) }}"
                        class="tab w-full {{ $activeTab === 'vacancies' ? 'active' : '' }}"
                    >
                        Модерация вакансий
                    </a>
                </div>

                <div class="admin-body">
                    @if($activeTab === 'users')
                        <div class="table-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Пользователь</th>
                                        <th>Роль</th>
                                        <th>Статус</th>
                                        <th class="text-right">Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                        <tr>
                                            <td>
                                                <div class="flex items-center gap-4">
                                                    <div class="avatar table-avatar">{{ mb_substr((string) $user->name, 0, 1) }}</div>
                                                    <div>
                                                        <div class="table-main">{{ $user->name }}</div>
                                                        <div class="text-sm text-muted">{{ $user->email }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge badge-neutral role-badge">{{ $user->role }}</span>
                                            </td>
                                            <td>
                                                @if($user->is_blocked)
                                                    <span class="badge badge-danger">Заблокирован</span>
                                                @else
                                                    <span class="badge badge-success">Активен</span>
                                                @endif
                                            </td>
                                            <td class="text-right">
                                                @if($user->role !== 'admin')
                                                    <form method="POST" action="{{ route('admin.users.toggle-block', ['user' => $user->id]) }}">
                                                        @csrf
                                                        <button
                                                            type="submit"
                                                            class="btn {{ $user->is_blocked ? 'btn-success' : 'btn-danger' }} admin-action-btn"
                                                        >
                                                            @if($user->is_blocked)
                                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                                    <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                                                </svg>
                                                                Разблокировать
                                                            @else
                                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"></circle>
                                                                    <path d="M8 8L16 16" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                                                                </svg>
                                                                Заблокировать
                                                            @endif
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    @if($activeTab === 'vacancies')
                        <div class="table-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Вакансия</th>
                                        <th>Работодатель</th>
                                        <th>Статус</th>
                                        <th class="text-right">Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($vacancies as $vacancy)
                                        @php
                                            $statusClass = match ($vacancy->status) {
                                                'open' => 'badge-success',
                                                'draft' => 'badge-neutral',
                                                'closed' => 'badge-primary',
                                                default => 'badge-danger',
                                            };
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="table-main">{{ $vacancy->title }}</div>
                                                <div class="text-sm text-muted">{{ $vacancy->created_at?->diffForHumans() }}</div>
                                            </td>
                                            <td>
                                                <div class="table-main">{{ $vacancy->employer?->name ?? 'Неизвестно' }}</div>
                                            </td>
                                            <td>
                                                <span class="badge {{ $statusClass }}">{{ mb_strtoupper((string) $vacancy->status) }}</span>
                                            </td>
                                            <td class="text-right">
                                                <div class="flex items-center justify-end gap-2">
                                                    @if($vacancy->status !== 'archived')
                                                        <form
                                                            method="POST"
                                                            action="{{ route('admin.vacancies.archive', ['vacancy' => $vacancy->id]) }}"
                                                            onsubmit="return confirm('Вы уверены, что хотите отправить эту вакансию в архив?');"
                                                        >
                                                            @csrf
                                                            <button type="submit" class="btn-icon danger-icon" title="В архив">
                                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                                    <path d="M3 6H21" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                                                                    <path d="M8 6V4H16V6" stroke="currentColor" stroke-width="2"></path>
                                                                    <path d="M6 6L7 20H17L18 6" stroke="currentColor" stroke-width="2"></path>
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        @endif
@endsection
