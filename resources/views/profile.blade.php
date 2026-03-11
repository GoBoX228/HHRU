<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль | Za_raboty</title>
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
        @if(($currentUser?->role ?? '') !== 'freelancer')
            <div class="text-center text-muted access-state">Доступ запрещен. Только для фрилансеров.</div>
        @else
            @php
                $skills = collect($profile?->skills ?? [])->filter()->values();
                $birthDateValue = $profile?->birth_date?->format('Y-m-d') ?? '';
                $genderValue = $profile?->gender ?? 'other';
                $genderLabel = match($genderValue) {
                    'male' => 'Мужской',
                    'female' => 'Женский',
                    'other' => 'Другой',
                    default => 'Не указано',
                };
            @endphp

            <div class="content-narrow">
                <div class="flex justify-between items-center mb-8 profile-head">
                    <h1 class="page-title profile-title">Мой профиль</h1>
                    <button type="button" class="btn btn-outline" data-profile-edit-open>Редактировать профиль</button>
                </div>

                @if(session('profile_saved'))
                    <div class="alert alert-success mb-8">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M20 7L9 18L4 13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                        Профиль успешно сохранен!
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger mb-8">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"></circle>
                            <path d="M12 8V13" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                            <circle cx="12" cy="16.5" r="1" fill="currentColor"></circle>
                        </svg>
                        {{ $errors->first() }}
                    </div>
                @endif

                <div class="card profile-card">
                    <div class="flex items-center gap-6 mb-8 pb-8 profile-summary">
                        <div class="avatar avatar-lg">{{ mb_substr($currentUser->name, 0, 1) }}</div>
                        <div>
                            <h2 class="text-2xl mb-2">{{ $currentUser->name }}</h2>
                            <div class="flex items-center gap-4 text-sm text-muted profile-meta">
                                <span class="flex items-center gap-2">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M4 6H20V18H4V6Z" stroke="currentColor" stroke-width="2"></path>
                                        <path d="M4 7L12 13L20 7" stroke="currentColor" stroke-width="2"></path>
                                    </svg>
                                    {{ $currentUser->email }}
                                </span>
                                <span class="flex items-center gap-2">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M3 7H21V20H3V7Z" stroke="currentColor" stroke-width="2"></path>
                                        <path d="M9 7V5C9 3.89543 9.89543 3 11 3H13C14.1046 3 15 3.89543 15 5V7" stroke="currentColor" stroke-width="2"></path>
                                    </svg>
                                    {{ $profile?->specialization ?: 'Специализация не указана' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('profile.update') }}" class="hidden" data-profile-edit-form>
                        @csrf
                        <div class="grid grid-cols-2 gap-6 mb-6">
                            <div class="form-group">
                                <label for="specialization" class="form-label">Специализация</label>
                                <input id="specialization" type="text" name="specialization" required value="{{ old('specialization', $profile?->specialization ?? '') }}" class="form-control" placeholder="например, Frontend разработчик">
                            </div>
                            <div class="form-group">
                                <label for="experience" class="form-label">Уровень опыта</label>
                                <select id="experience" name="experience" class="form-control">
                                    <option value="">Выберите уровень...</option>
                                    @foreach(['Junior', 'Middle', 'Senior', 'Lead'] as $experience)
                                        <option value="{{ $experience }}" @selected(old('experience', $profile?->experience) === $experience)>{{ $experience }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="birth_date" class="form-label">Дата рождения</label>
                                <input id="birth_date" type="date" name="birth_date" value="{{ old('birth_date', $birthDateValue) }}" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="gender" class="form-label">Пол</label>
                                <select id="gender" name="gender" class="form-control">
                                    <option value="male" @selected(old('gender', $genderValue) === 'male')>Мужской</option>
                                    <option value="female" @selected(old('gender', $genderValue) === 'female')>Женский</option>
                                    <option value="other" @selected(old('gender', $genderValue) === 'other')>Другой</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group mb-6">
                            <label for="skills" class="form-label">Навыки (через запятую, макс. 20)</label>
                            <input id="skills" type="text" name="skills" value="{{ old('skills', $skills->implode(', ')) }}" class="form-control" placeholder="React, TypeScript, Node.js...">
                        </div>

                        <div class="form-group mb-8">
                            <label for="about" class="form-label">О себе (макс. 1000 символов)</label>
                            <textarea id="about" name="about" rows="5" maxlength="1000" class="form-control" data-about-input placeholder="Расскажите о себе...">{{ old('about', $profile?->about ?? '') }}</textarea>
                            <div class="text-right text-sm text-muted mt-2">
                                <span data-about-counter>{{ mb_strlen(old('about', $profile?->about ?? '')) }}</span> / 1000
                            </div>
                        </div>

                        <div class="flex justify-end gap-2 pt-4 profile-form-actions">
                            <button type="button" class="btn btn-outline" data-profile-edit-cancel>Отмена</button>
                            <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                        </div>
                    </form>

                    <div class="flex flex-col gap-8" data-profile-view>
                        <section>
                            <h3 class="section-title flex items-center gap-2">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true" class="profile-section-icon">
                                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"></circle>
                                    <path d="M12 11V16" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                                    <circle cx="12" cy="8" r="1" fill="currentColor"></circle>
                                </svg>
                                Обо мне
                            </h3>
                            <p class="profile-text">{{ $profile?->about ?: 'Описание не предоставлено.' }}</p>
                        </section>

                        <section>
                            <h3 class="section-title flex items-center gap-2">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true" class="profile-section-icon">
                                    <path d="M3 7H21V20H3V7Z" stroke="currentColor" stroke-width="2"></path>
                                    <path d="M9 7V5C9 3.89543 9.89543 3 11 3H13C14.1046 3 15 3.89543 15 5V7" stroke="currentColor" stroke-width="2"></path>
                                </svg>
                                Навыки и экспертиза
                            </h3>
                            <div class="profile-panel">
                                <div class="grid grid-cols-2 gap-6">
                                    <div>
                                        <div class="text-sm text-muted mb-2">Уровень опыта</div>
                                        <div class="font-medium">{{ $profile?->experience ?: 'Не указано' }}</div>
                                    </div>
                                    <div>
                                        <div class="text-sm text-muted mb-2">Навыки</div>
                                        <div class="flex gap-2 skills-wrap">
                                            @if($skills->isNotEmpty())
                                                @foreach($skills as $skill)
                                                    <span class="badge badge-primary">{{ $skill }}</span>
                                                @endforeach
                                            @else
                                                <span class="text-sm text-muted">Навыки не указаны</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section>
                            <h3 class="section-title flex items-center gap-2">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true" class="profile-section-icon">
                                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"></circle>
                                    <path d="M12 7V12L15 14" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                                </svg>
                                Личная информация
                            </h3>
                            <div class="profile-panel">
                                <div class="grid grid-cols-2 gap-6">
                                    <div>
                                        <div class="text-sm text-muted mb-2">Дата рождения</div>
                                        <div class="font-medium">{{ $profile?->birth_date?->format('Y-m-d') ?: 'Не указано' }}</div>
                                    </div>
                                    <div>
                                        <div class="text-sm text-muted mb-2">Пол</div>
                                        <div class="font-medium">{{ $genderLabel }}</div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        @endif
    </main>
</div>
<script>
    (() => {
        const openButton = document.querySelector('[data-profile-edit-open]');
        const cancelButton = document.querySelector('[data-profile-edit-cancel]');
        const editForm = document.querySelector('[data-profile-edit-form]');
        const viewBlock = document.querySelector('[data-profile-view]');
        const aboutInput = document.querySelector('[data-about-input]');
        const aboutCounter = document.querySelector('[data-about-counter]');

        if (aboutInput && aboutCounter) {
            aboutInput.addEventListener('input', () => {
                aboutCounter.textContent = String(aboutInput.value.length);
            });
        }

        if (!openButton || !cancelButton || !editForm || !viewBlock) {
            return;
        }

        const shouldOpenEdit = {{ $errors->any() ? 'true' : 'false' }};

        const setEditingState = (isEditing) => {
            editForm.classList.toggle('hidden', !isEditing);
            viewBlock.classList.toggle('hidden', isEditing);
            openButton.classList.toggle('hidden', isEditing);
        };

        openButton.addEventListener('click', () => setEditingState(true));
        cancelButton.addEventListener('click', () => setEditingState(false));

        setEditingState(shouldOpenEdit);
    })();
</script>
</body>
</html>
