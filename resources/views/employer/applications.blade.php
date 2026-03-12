@extends('layouts.app')

@section('title', 'Отклики работодателя')

@section('content')
    @if(! $canAccess)
        <div class="text-center text-muted" style="padding: 48px 0;">
            Доступ запрещен. Только для работодателей.
        </div>
    @elseif(! $vacancy)
        <div class="text-center" style="padding: 48px 0;">
            <h2 class="page-title">Вакансия не найдена</h2>
            <a href="{{ route('employer.dashboard') }}" class="btn btn-outline mt-4">Назад к вакансиям</a>
        </div>
    @else
        <div style="max-width: 800px; margin: 0 auto;">
            <a href="{{ route('employer.dashboard') }}" class="btn-icon flex items-center gap-2 mb-4 back-link">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M19 12H5" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                    <path d="M12 19L5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                </svg>
                Назад к вакансиям
            </a>

            <div class="mb-8">
                <h1 class="page-title">Отклики на {{ $vacancy->title }}</h1>
                <p class="page-subtitle">Просматривайте и управляйте кандидатами.</p>
            </div>

            <div class="flex flex-col gap-6">
                @if($vacancyApps->isEmpty())
                    <div class="card text-center" style="padding: 48px 24px;">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" class="empty-icon">
                            <circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="2"></circle>
                            <path d="M4 21C4 17.7 7.1 15 11 15H13C16.9 15 20 17.7 20 21" stroke="currentColor" stroke-width="2"></path>
                        </svg>
                        <h3 class="section-title">Пока нет откликов</h3>
                        <p class="text-muted">Кандидаты появятся здесь, когда откликнутся.</p>
                    </div>
                @else
                    @foreach($vacancyApps as $application)
                        @php
                            $freelancer = $application->freelancer;
                            $profile = $freelancer?->freelancerProfile;
                            $skills = $profile?->skills;
                            $skills = is_array($skills) ? $skills : [];
                        @endphp

                        @if($freelancer)
                            <div class="card">
                                <div class="flex justify-between items-start application-wrap">
                                    <div class="flex-grow">
                                        <div class="flex items-center gap-4 mb-6">
                                            <div class="avatar">{{ mb_substr($freelancer->name, 0, 1) }}</div>
                                            <div>
                                                <h3 class="text-xl" style="font-weight: 700;">{{ $freelancer->name }}</h3>
                                                <div class="flex items-center gap-2 text-sm text-muted wrap-line">
                                                    <span class="flex items-center gap-2">
                                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                            <path d="M9 6V4C9 2.9 9.9 2 11 2H13C14.1 2 15 2.9 15 4V6" stroke="currentColor" stroke-width="2"></path>
                                                            <path d="M3 6H21V20H3V6Z" stroke="currentColor" stroke-width="2"></path>
                                                        </svg>
                                                        {{ $profile?->specialization ?: 'Специализация не указана' }}
                                                    </span>
                                                    <span>•</span>
                                                    <span class="flex items-center gap-2">
                                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                            <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"></circle>
                                                            <path d="M12 7V12L15 14" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                                                        </svg>
                                                        Отклик отправлен {{ $application->created_at?->diffForHumans() }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="flex flex-col gap-4">
                                            <div>
                                                <h4 class="text-sm" style="font-weight: 600; margin-bottom: 8px;">Сопроводительное письмо</h4>
                                                <p class="text-sm text-muted cover-letter-box">{{ $application->cover_letter ?: 'Не указано' }}</p>
                                            </div>

                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <h4 class="text-sm" style="font-weight: 600; margin-bottom: 4px;">Опыт</h4>
                                                    <p class="text-sm text-muted">{{ $profile?->experience ?: 'Не указан' }}</p>
                                                </div>
                                                <div>
                                                    <h4 class="text-sm" style="font-weight: 600; margin-bottom: 4px;">Навыки</h4>
                                                    <div class="flex gap-2 skills-wrap">
                                                        @forelse($skills as $skill)
                                                            <span class="badge badge-primary badge-sm">{{ $skill }}</span>
                                                        @empty
                                                            <span class="text-sm text-muted">Не указаны</span>
                                                        @endforelse
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex flex-col items-end gap-4 app-status-col">
                                        <div class="badge
                                            @if($application->status === 'pending') badge-warning
                                            @elseif($application->status === 'accepted') badge-success
                                            @elseif($application->status === 'rejected') badge-danger
                                            @else badge-neutral
                                            @endif
                                        " style="padding: 6px 12px; font-size: 14px;">
                                            {{ strtoupper($application->status) }}
                                        </div>

                                        @if($application->status === 'pending' && $vacancy->status === 'open')
                                            <div class="flex flex-col gap-2 w-full mt-4">
                                                <form method="POST" action="{{ route('employer.applications.status', ['vacancyId' => $vacancy->id, 'applicationId' => $application->id]) }}">
                                                    @csrf
                                                    <input type="hidden" name="status" value="accepted">
                                                    <button
                                                        type="submit"
                                                        class="btn btn-success w-full justify-center"
                                                        onclick="return confirm('Вы уверены, что хотите принять этот отклик? Это закроет вакансию и отклонит остальные отклики.')"
                                                    >
                                                        Принять
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('employer.applications.status', ['vacancyId' => $vacancy->id, 'applicationId' => $application->id]) }}">
                                                    @csrf
                                                    <input type="hidden" name="status" value="rejected">
                                                    <button type="submit" class="btn btn-danger w-full justify-center">Отклонить</button>
                                                </form>
                                            </div>
                                        @endif

                                        @if($application->status === 'accepted')
                                            <a href="{{ route('chats') }}" class="btn btn-primary w-full mt-4 justify-center">
                                                Перейти в чаты
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                @endif
            </div>
        </div>
    @endif
@endsection
