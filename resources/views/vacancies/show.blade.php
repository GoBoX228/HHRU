@extends('layouts.app')

@section('title', $vacancy?->title ?? 'Вакансия')

@section('content')
    @if(! $vacancy)
        <div class="text-center" style="padding: 48px 0;">
            <h2 class="page-title">Вакансия не найдена</h2>
            <a href="{{ route('home') }}" class="btn btn-outline mt-4">На главную</a>
        </div>
    @else
        @php
            $companyName = $employer?->company_name ?: 'Компания не указана';
            $companyField = $employer?->company_field ?: 'Не указано';
            $companyDescription = $employer?->company_description ?: 'Описание не предоставлено.';
            $requiredSkills = $vacancy->required_skills;
            $requiredSkills = is_array($requiredSkills) ? $requiredSkills : [];
        @endphp

        <div style="max-width: 800px; margin: 0 auto;">
            <a href="{{ route('home') }}" class="btn-icon flex items-center gap-2 mb-4 back-link">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M19 12H5" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                    <path d="M12 19L5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                </svg>
                Назад
            </a>

            <div class="card" style="padding: 32px;">
                <div class="flex justify-between items-start mb-8 responsive-wrap">
                    <div>
                        <h1 class="page-title" style="margin-bottom: 16px;">{{ $vacancy->title }}</h1>
                        <div class="flex items-center gap-4 text-sm text-muted wrap-line">
                            <span class="flex items-center gap-2" style="color: var(--text); font-weight: 500;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M3 7H21V20H3V7Z" stroke="currentColor" stroke-width="2"></path>
                                    <path d="M9 7V5C9 3.9 9.9 3 11 3H13C14.1 2 15 2.9 15 5V7" stroke="currentColor" stroke-width="2"></path>
                                </svg>
                                {{ $companyName }}
                            </span>
                            <span class="flex items-center gap-2">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"></circle>
                                    <path d="M12 7V12L15 14" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                                </svg>
                                Опубликовано {{ $vacancy->created_at?->diffForHumans() }}
                            </span>
                            <span class="badge badge-primary">{{ match($vacancy->status) { 'open' => 'Открыта', 'draft' => 'Черновик', 'closed' => 'Закрыта', 'archived' => 'В архиве', default => (string) $vacancy->status, } }}</span>
                        </div>
                    </div>

                    <div class="budget-box">
                        <div class="text-sm text-muted mb-2">Бюджет</div>
                        <div class="text-2xl">{{ number_format((int) $vacancy->budget, 0, '.', ' ') }} {{ $vacancy->currency }}</div>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-8">
                    <div style="grid-column: span 2;">
                        <section class="mb-8">
                            <h3 class="section-title">Описание</h3>
                            <div style="white-space: pre-wrap; color: var(--text-muted);">{{ $vacancy->description }}</div>
                        </section>

                        <section>
                            <h3 class="section-title">Требуемые навыки</h3>
                            <div class="flex gap-2 skills-wrap">
                                @forelse($requiredSkills as $skill)
                                    <span class="badge badge-neutral">{{ $skill }}</span>
                                @empty
                                    <span class="text-sm text-muted">Не указаны</span>
                                @endforelse
                            </div>
                        </section>
                    </div>

                    <div class="flex flex-col gap-6">
                        <div class="info-box">
                            <h3 class="text-sm text-muted mb-4 details-heading">Детали работы</h3>
                            <div class="mb-4">
                                <div class="text-sm text-muted">Специализация</div>
                                <div class="info-value">{{ $vacancy->specialization ?: 'Не указана' }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-muted">Уровень опыта</div>
                                <div class="info-value">{{ $vacancy->required_experience ?: 'Не указан' }}</div>
                            </div>
                        </div>

                        <div class="info-box">
                            <h3 class="text-sm text-muted mb-4 details-heading">О компании</h3>
                            <div class="mb-4">
                                <div class="text-sm text-muted">Отрасль</div>
                                <div class="info-value">{{ $companyField }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-muted">Описание</div>
                                <div style="margin-top: 4px; font-size: 14px; color: var(--text-muted);">
                                    {{ $companyDescription }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if($currentUser && $currentUser->role === 'freelancer' && $vacancy->status === 'open')
                    <div style="margin-top: 40px; padding-top: 32px; border-top: 1px solid var(--border);">
                        @if($existingApplication)
                            <div class="alert alert-success" style="align-items: flex-start; padding: 24px;">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" style="flex-shrink: 0;">
                                    <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                                </svg>
                                <div>
                                    <h3 style="font-size: 18px; font-weight: 500; margin-bottom: 4px;">Вы откликнулись на эту позицию</h3>
                                    <p style="margin-bottom: 8px;">Статус: <strong>{{ match($existingApplication->status) { 'pending' => 'На рассмотрении', 'accepted' => 'Принят', 'rejected' => 'Отклонен', 'canceled' => 'Отменен', default => (string) $existingApplication->status, } }}</strong></p>
                                    <p class="text-sm" style="opacity: 0.8;">
                                        Отклик отправлен {{ $existingApplication->created_at?->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        @else
                            <button
                                type="button"
                                id="open-apply-form"
                                class="btn btn-primary {{ old('coverLetter') ? 'hidden' : '' }}"
                                style="padding: 12px 32px; font-size: 16px;"
                            >
                                Откликнуться
                            </button>

                            <form
                                method="POST"
                                action="{{ route('vacancies.apply', ['id' => $vacancy->id]) }}"
                                id="apply-form"
                                class="{{ old('coverLetter') ? '' : 'hidden' }}"
                                style="background: var(--bg); padding: 24px; border-radius: 12px;"
                            >
                                @csrf
                                <h3 class="section-title">Отправить отклик</h3>
                                <div class="form-group">
                                    <label for="coverLetter" class="form-label">
                                        Сопроводительное письмо (до 1000 символов)
                                    </label>
                                    <textarea
                                        id="coverLetter"
                                        name="coverLetter"
                                        rows="6"
                                        maxlength="1000"
                                        required
                                        class="form-control"
                                        placeholder="Расскажите работодателю, почему вы отлично подходите для этого проекта..."
                                    >{{ old('coverLetter') }}</textarea>
                                    <div class="text-right text-sm text-muted mt-2">
                                        <span id="cover-letter-counter">{{ mb_strlen(old('coverLetter', '')) }}</span> / 1000
                                    </div>
                                </div>
                                <div class="flex justify-end gap-2 mt-4">
                                    <button type="button" id="cancel-apply-form" class="btn btn-outline">Отмена</button>
                                    <button type="submit" class="btn btn-primary">Отправить отклик</button>
                                </div>
                            </form>
                        @endif
                    </div>
                @endif

                @if(! $currentUser)
                    <div class="text-center" style="margin-top: 40px; padding-top: 32px; border-top: 1px solid var(--border);">
                        <p class="text-muted mb-4">Вам нужно войти как фрилансер, чтобы откликнуться.</p>
                        <div class="flex justify-center gap-4">
                            <a href="{{ route('login') }}" class="btn btn-outline">Войти</a>
                            <a href="{{ route('register') }}" class="btn btn-primary">Регистрация</a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <script>
        (() => {
            const openButton = document.getElementById('open-apply-form');
            const cancelButton = document.getElementById('cancel-apply-form');
            const applyForm = document.getElementById('apply-form');
            const coverLetter = document.getElementById('coverLetter');
            const counter = document.getElementById('cover-letter-counter');

            if (openButton && applyForm) {
                openButton.addEventListener('click', () => {
                    openButton.classList.add('hidden');
                    applyForm.classList.remove('hidden');
                });
            }

            if (cancelButton && applyForm && openButton) {
                cancelButton.addEventListener('click', () => {
                    applyForm.classList.add('hidden');
                    openButton.classList.remove('hidden');
                });
            }

            if (coverLetter && counter) {
                coverLetter.addEventListener('input', () => {
                    counter.textContent = coverLetter.value.length.toString();
                });
            }
        })();
    </script>
@endsection

