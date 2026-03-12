@extends('layouts.app')

@section('title', 'Мои вакансии')

@section('content')
    @if(! $canAccess)
        <div class="text-center text-muted" style="padding: 48px 0;">
            Доступ запрещен. Только для работодателей.
        </div>
    @else
        <div class="flex justify-between items-center mb-8 employer-head">
            <div>
                <h1 class="page-title">Мои вакансии</h1>
                <p class="page-subtitle">Управляйте своими вакансиями и откликами.</p>
            </div>
            <button type="button" id="open-create-form" class="btn btn-primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M12 5V19" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                    <path d="M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                </svg>
                Создать вакансию
            </button>
        </div>

        <div
            id="create-vacancy-form"
            class="card {{ $errors->any() ? '' : 'hidden' }}"
            style="padding: 32px; margin-bottom: 32px;"
        >
            <h2 class="section-title mb-6">Создать новую вакансию</h2>
            <form method="POST" action="{{ route('employer.vacancies.store') }}">
                @csrf

                <div class="grid grid-cols-2 gap-6 mb-6">
                    <div class="form-group" style="grid-column: span 2;">
                        <label class="form-label">Название (макс. 150 симв.)</label>
                        <input
                            type="text"
                            name="title"
                            required
                            maxlength="150"
                            value="{{ old('title') }}"
                            class="form-control"
                            placeholder="Например, Senior Frontend разработчик"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">Специализация</label>
                        <input
                            type="text"
                            name="specialization"
                            required
                            value="{{ old('specialization') }}"
                            class="form-control"
                            placeholder="Например, Frontend"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">Требуемый опыт</label>
                        <select name="requiredExperience" required class="form-control">
                            <option value="">Выберите уровень...</option>
                            @foreach(['Junior', 'Middle', 'Senior', 'Lead'] as $level)
                                <option value="{{ $level }}" @selected(old('requiredExperience') === $level)>{{ $level }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group" style="grid-column: span 2;">
                        <label class="form-label">Требуемые навыки (через запятую, макс. 20)</label>
                        <input
                            type="text"
                            name="requiredSkills"
                            value="{{ old('requiredSkills') }}"
                            class="form-control"
                            placeholder="React, TypeScript, Node.js..."
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">Бюджет</label>
                        <input
                            type="number"
                            name="budget"
                            required
                            min="0"
                            value="{{ old('budget', 0) }}"
                            class="form-control"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">Валюта</label>
                        <select name="currency" class="form-control">
                            @foreach(['USD', 'EUR', 'RUB'] as $currency)
                                <option value="{{ $currency }}" @selected(old('currency', 'USD') === $currency)>{{ $currency }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group" style="grid-column: span 2;">
                        <label class="form-label">Описание (макс. 3000 симв.)</label>
                        <textarea
                            name="description"
                            required
                            rows="6"
                            maxlength="3000"
                            class="form-control"
                            placeholder="Опишите проект и обязанности..."
                        >{{ old('description') }}</textarea>
                    </div>

                    <div class="form-group" style="grid-column: span 2;">
                        <label class="form-label">Статус</label>
                        <select name="status" class="form-control">
                            <option value="draft" @selected(old('status', 'draft') === 'draft')>Черновик (Скрыто)</option>
                            <option value="open" @selected(old('status') === 'open')>Открыта (Опубликовано)</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-4" style="border-top: 1px solid var(--border);">
                    <button type="button" id="cancel-create-form" class="btn btn-outline">Отмена</button>
                    <button type="submit" class="btn btn-primary">Создать вакансию</button>
                </div>
            </form>
        </div>

        <div class="flex flex-col gap-4">
            @if($myVacancies->isEmpty())
                <div class="card text-center" style="padding: 48px 24px;">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" class="empty-icon">
                        <path d="M3 7H21V20H3V7Z" stroke="currentColor" stroke-width="2"></path>
                        <path d="M9 7V5C9 3.9 9.9 3 11 3H13C14.1 3 15 3.9 15 5V7" stroke="currentColor" stroke-width="2"></path>
                    </svg>
                    <h3 class="section-title">Пока нет вакансий</h3>
                    <p class="text-muted">Создайте первую вакансию, чтобы начать получать отклики.</p>
                    <button type="button" class="btn btn-primary mt-4" id="open-create-form-empty">Создать вакансию</button>
                </div>
            @else
                @foreach($myVacancies as $vacancy)
                    @php
                        $totalApplications = (int) ($vacancy->applications_count ?? 0);
                        $pendingApplications = (int) ($vacancy->pending_applications_count ?? 0);
                    @endphp
                    <div class="card card-hover">
                        <div class="flex justify-between items-start responsive-wrap">
                            <div class="flex-grow">
                                <div class="flex items-center gap-4 mb-2">
                                    <h2 class="text-xl">{{ $vacancy->title }}</h2>
                                    <span class="badge
                                        @if($vacancy->status === 'open') badge-success
                                        @elseif($vacancy->status === 'draft') badge-neutral
                                        @elseif($vacancy->status === 'closed') badge-primary
                                        @else badge-danger
                                        @endif
                                    ">
                                        {{ strtoupper($vacancy->status) }}
                                    </span>
                                </div>
                                <div class="text-sm text-muted">
                                    Создано {{ $vacancy->created_at?->diffForHumans() }}
                                </div>

                                <div class="mt-4 flex items-center gap-6">
                                    <div class="flex items-center gap-2 text-sm text-muted">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M16 21V19C16 17.3 14.7 16 13 16H5C3.3 16 2 17.3 2 19V21" stroke="currentColor" stroke-width="2"></path>
                                            <circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="2"></circle>
                                            <path d="M22 21V19C22 17.5 20.9 16.2 19.5 16" stroke="currentColor" stroke-width="2"></path>
                                            <path d="M16.5 3.1C17.9 3.5 19 4.8 19 6.5C19 8.2 17.9 9.5 16.5 9.9" stroke="currentColor" stroke-width="2"></path>
                                        </svg>
                                        <span style="font-weight: 500; color: var(--text);">{{ $totalApplications }}</span> всего откликов
                                        @if($pendingApplications > 0)
                                            <span class="badge badge-warning" style="margin-left: 8px;">
                                                {{ $pendingApplications }} ожидают
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 actions-wrap">
                                <a href="{{ route('employer.applications.index', ['id' => $vacancy->id]) }}" class="btn btn-outline">
                                    Смотреть отклики
                                </a>

                                @if($vacancy->status === 'draft')
                                    <form method="POST" action="{{ route('employer.vacancies.status', ['id' => $vacancy->id]) }}">
                                        @csrf
                                        <input type="hidden" name="status" value="open">
                                        <button type="submit" class="btn btn-outline">Опубликовать</button>
                                    </form>
                                @endif

                                @if(in_array($vacancy->status, ['open', 'draft'], true))
                                    <form method="POST" action="{{ route('employer.vacancies.status', ['id' => $vacancy->id]) }}">
                                        @csrf
                                        <input type="hidden" name="status" value="archived">
                                        <button type="submit" class="btn btn-danger">В архив</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    @endif

    <script>
        (() => {
            const formCard = document.getElementById('create-vacancy-form');
            const openButtons = [
                document.getElementById('open-create-form'),
                document.getElementById('open-create-form-empty')
            ];
            const cancelButton = document.getElementById('cancel-create-form');

            openButtons.forEach((button) => {
                if (!button || !formCard) {
                    return;
                }

                button.addEventListener('click', () => {
                    formCard.classList.remove('hidden');
                    formCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
                });
            });

            if (cancelButton && formCard) {
                cancelButton.addEventListener('click', () => formCard.classList.add('hidden'));
            }
        })();
    </script>
@endsection
