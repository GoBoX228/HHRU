@extends('layouts.app')
@section('title', 'My Applications')
@section('content')
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
                                        <a href="{{ route('vacancies.show', ['id' => $vacancy->id]) }}" class="text-xl application-link">{{ $vacancy->title }}</a>
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
@endsection
