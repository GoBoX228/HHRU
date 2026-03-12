@extends('layouts.app')
@section('title', 'Profile')
@section('content')
@if(($currentUser?->role ?? '') !== 'freelancer')
            <div class="text-center text-muted access-state">Доступ запрещен. Только для фрилансеров.</div>
        @else
            @php
                $skills = collect($profile?->skills ?? [])->filter()->values();
                $skillsInputValue = old('skills', $skills->implode(', '));
                $selectedEditSkills = collect(explode(',', (string) $skillsInputValue))
                    ->map(static fn (string $skill): string => trim($skill))
                    ->filter()
                    ->unique()
                    ->take(20)
                    ->values();
                $availableSpecializations = [
                    'Frontend разработчик',
                    'Backend разработчик',
                    'Fullstack разработчик',
                    'Mobile разработчик',
                    'DevOps инженер',
                    'QA инженер',
                    'UI/UX дизайнер',
                    'Data Analyst',
                    'Data Scientist',
                    'Project Manager',
                    'Product Manager',
                    'Бизнес-аналитик',
                    'Системный администратор',
                    'Кибербезопасность',
                ];
                $selectedSpecialization = old('specialization', $profile?->specialization ?? '');
                $availableSkills = [
                    'PHP',
                    'Laravel',
                    'JavaScript',
                    'TypeScript',
                    'React',
                    'Vue.js',
                    'Node.js',
                    'HTML',
                    'CSS',
                    'Tailwind CSS',
                    'Sass',
                    'Bootstrap',
                    'MySQL',
                    'PostgreSQL',
                    'SQLite',
                    'Redis',
                    'REST API',
                    'GraphQL',
                    'Docker',
                    'Git',
                    'Linux',
                    'Nginx',
                    'Apache',
                    'Python',
                    'Django',
                    'Java',
                    'Spring',
                    'C#',
                    '.NET',
                    'Go',
                    'DevOps',
                    'CI/CD',
                ];
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

                    <form method="POST" action="{{ route('profile.update') }}" class="hidden" data-profile-edit-form data-open-on-load="{{ $errors->any() ? '1' : '0' }}">
                        @csrf
                        <div class="grid grid-cols-2 gap-6 mb-6">
                            <div class="form-group">
                                <label for="specialization" class="form-label">Специализация</label>
                                <select id="specialization" name="specialization" required class="form-control">
                                    <option value="">Выберите специализацию...</option>
                                    @foreach($availableSpecializations as $specializationOption)
                                        <option value="{{ $specializationOption }}" @selected($selectedSpecialization === $specializationOption)>
                                            {{ $specializationOption }}
                                        </option>
                                    @endforeach
                                    @if($selectedSpecialization !== '' && !in_array($selectedSpecialization, $availableSpecializations, true))
                                        <option value="{{ $selectedSpecialization }}" selected>
                                            {{ $selectedSpecialization }}
                                        </option>
                                    @endif
                                </select>
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
                            <label for="skill-select" class="form-label">Навыки (макс. 20)</label>
                            <div class="skills-picker">
                                <select id="skill-select" class="form-control" data-skill-select>
                                    <option value="">Выберите навык...</option>
                                    @foreach($availableSkills as $skillOption)
                                        <option value="{{ $skillOption }}">{{ $skillOption }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <input
                                id="skills"
                                type="hidden"
                                name="skills"
                                value="{{ $selectedEditSkills->implode(', ') }}"
                                data-skills-input
                            >

                            <div class="skills-selected mt-2" data-skills-selected>
                                @if($selectedEditSkills->isNotEmpty())
                                    @foreach($selectedEditSkills as $selectedSkill)
                                        <span class="badge badge-primary skill-chip">
                                            {{ $selectedSkill }}
                                            <button
                                                type="button"
                                                class="skill-chip-remove"
                                                data-skill-remove="{{ $selectedSkill }}"
                                                aria-label="Удалить навык {{ $selectedSkill }}"
                                            >
                                                &times;
                                            </button>
                                        </span>
                                    @endforeach
                                @else
                                    <span class="text-sm text-muted" data-skills-empty>
                                        Навыки не выбраны
                                    </span>
                                @endif
                            </div>
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
@endsection
