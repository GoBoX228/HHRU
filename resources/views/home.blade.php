@extends('layouts.app')
@section('title', 'Vacancies')
@section('content')
<div class="flex justify-between items-center mb-8 home-head">
            <div>
                <h1 class="page-title">Найдите свой следующий проект</h1>
                <p class="page-subtitle">Откройте для себя возможности, соответствующие вашим навыкам.</p>
            </div>

            <form method="GET" action="{{ route('home') }}" class="flex gap-4 home-filters">
                <div class="search-wrap">
                    <svg class="search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"></circle>
                        <path d="M20 20L17 17" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                    </svg>
                    <input
                        type="text"
                        name="search"
                        value="{{ $searchTerm }}"
                        placeholder="Поиск вакансий..."
                        class="form-control search-input"
                    >
                </div>

                <select name="specialization" class="form-control specialization-select">
                    <option value="">Все специализации</option>
                    @foreach($uniqueSpecializations as $spec)
                        <option value="{{ $spec }}" @selected($specialization === $spec)>{{ $spec }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        <div class="grid grid-cols-1 gap-grid">
            @if($activeVacancies->isEmpty())
                <div class="card text-center empty-card">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" class="empty-icon">
                        <path d="M3 7H21V20H3V7Z" stroke="currentColor" stroke-width="2"></path>
                        <path d="M9 7V5C9 3.89543 9.89543 3 11 3H13C14.1046 3 15 3.89543 15 5V7" stroke="currentColor" stroke-width="2"></path>
                    </svg>
                    <h3 class="section-title">Вакансии не найдены</h3>
                    <p class="text-muted">Попробуйте изменить фильтры поиска.</p>
                </div>
            @else
                @foreach($activeVacancies as $vacancy)
                    @php
                        $skills = is_array($vacancy->required_skills) ? $vacancy->required_skills : [];
                    @endphp
                    <a href="{{ route('vacancies.show', ['id' => $vacancy->id]) }}" class="card card-hover">
                        <div class="flex justify-between items-start">
                            <div>
                                <h2 class="section-title vacancy-title">{{ $vacancy->title }}</h2>
                                <div class="flex items-center gap-2 text-sm text-muted mb-4">
                                    <span class="company-name">
                                        {{ $vacancy->employerProfile?->company_name ?? 'Неизвестная компания' }}
                                    </span>
                                    <span>•</span>
                                    <span class="flex items-center gap-2">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"></circle>
                                            <path d="M12 7V12L15 14" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                                        </svg>
                                        {{ $vacancy->created_at?->diffForHumans() }}
                                    </span>
                                </div>
                            </div>

                            <div class="text-right">
                                <div class="text-xl">{{ number_format((int) $vacancy->budget, 0, '.', ' ') }} {{ $vacancy->currency }}</div>
                            </div>
                        </div>

                        <div class="flex gap-2 mb-4 skills-wrap">
                            <span class="badge badge-primary">{{ $vacancy->specialization }}</span>
                            <span class="badge badge-neutral">{{ $vacancy->required_experience }}</span>

                            @foreach(array_slice($skills, 0, 3) as $skill)
                                <span class="badge badge-neutral">{{ $skill }}</span>
                            @endforeach

                            @if(count($skills) > 3)
                                <span class="badge badge-neutral">+{{ count($skills) - 3 }} еще</span>
                            @endif
                        </div>

                        <p class="text-sm text-muted vacancy-description">{{ $vacancy->description }}</p>
                    </a>
                @endforeach
            @endif
        </div>
@endsection
