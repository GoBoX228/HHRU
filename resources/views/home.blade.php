<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Za_raboty</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --bg: #f8fafc;
            --surface: #ffffff;
            --text: #0f172a;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --danger: #ef4444;
            --success: #22c55e;
            --warning: #eab308;
            --radius: 8px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: "Inter", system-ui, sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.5;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        button {
            cursor: pointer;
            font-family: inherit;
            border: none;
            background: none;
        }

        input, select, textarea {
            font-family: inherit;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .app-wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .main-content {
            flex: 1;
            padding: 32px 0;
        }

        .header {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 64px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 700;
            font-size: 20px;
        }

        .logo-icon {
            width: 32px;
            height: 32px;
            background: var(--primary);
            color: #fff;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .nav {
            display: flex;
            gap: 24px;
            align-items: center;
        }

        .nav-link {
            color: var(--text-muted);
            font-weight: 500;
            font-size: 14px;
            transition: color 0.2s;
        }

        .nav-link:hover {
            color: var(--text);
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .page-title {
            font-size: 30px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .page-subtitle {
            color: var(--text-muted);
            margin-bottom: 24px;
        }

        .section-title {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 16px;
        }

        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            margin-bottom: 16px;
        }

        .card-hover {
            transition: all 0.2s;
        }

        .card-hover:hover {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border-color: #c7d2fe;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: var(--radius);
            font-weight: 500;
            font-size: 14px;
            transition: all 0.2s;
        }

        .btn-primary {
            background: var(--primary);
            color: #fff;
        }

        .btn-primary:hover {
            background: var(--primary-hover);
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-primary {
            background: #e0e7ff;
            color: #3730a3;
        }

        .badge-neutral {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid var(--border);
        }

        .form-control {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .flex { display: flex; }
        .items-center { align-items: center; }
        .items-start { align-items: flex-start; }
        .justify-between { justify-content: space-between; }
        .justify-center { justify-content: center; }
        .gap-2 { gap: 8px; }
        .gap-4 { gap: 16px; }
        .mb-4 { margin-bottom: 16px; }
        .mb-8 { margin-bottom: 32px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-muted { color: var(--text-muted); }
        .text-sm { font-size: 14px; }
        .text-xl { font-size: 20px; font-weight: 600; }
        .grid { display: grid; }
        .grid-cols-1 { grid-template-columns: 1fr; }
        .gap-grid { gap: 16px; }

        .search-wrap {
            position: relative;
        }

        .search-icon {
            position: absolute;
            left: 12px;
            top: 11px;
            color: var(--text-muted);
        }

        .vacancy-description {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        @media (max-width: 900px) {
            .header-content {
                flex-direction: column;
                height: auto;
                padding: 16px 0;
                gap: 16px;
            }

            .home-head {
                flex-direction: column;
                align-items: flex-start;
            }

            .home-filters {
                width: 100%;
                flex-direction: column;
            }

            .search-wrap,
            .search-wrap .form-control,
            .home-filters select {
                width: 100% !important;
            }
        }
    </style>
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
            </nav>

            <div class="user-menu">
                <a href="#" class="nav-link">Войти</a>
                <a href="#" class="btn btn-primary">Регистрация</a>
            </div>
        </div>
    </header>

    <main class="main-content container">
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
                        class="form-control"
                        style="padding-left: 36px; width: 250px;"
                    >
                </div>

                <select name="specialization" class="form-control" style="width: 200px;">
                    <option value="">Все специализации</option>
                    @foreach($uniqueSpecializations as $spec)
                        <option value="{{ $spec }}" @selected($specialization === $spec)>{{ $spec }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        <div class="grid grid-cols-1 gap-grid">
            @if($activeVacancies->isEmpty())
                <div class="card text-center" style="padding: 48px 24px;">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" style="color: var(--border); margin: 0 auto 16px;">
                        <path d="M3 7H21V20H3V7Z" stroke="currentColor" stroke-width="2"></path>
                        <path d="M9 7V5C9 3.89543 9.89543 3 11 3H13C14.1046 3 15 3.89543 15 5V7" stroke="currentColor" stroke-width="2"></path>
                    </svg>
                    <h3 class="section-title">Вакансии не найдены</h3>
                    <p class="text-muted">Попробуйте изменить фильтры поиска.</p>
                </div>
            @else
                @foreach($activeVacancies as $vacancy)
                    <a href="#" class="card card-hover">
                        <div class="flex justify-between items-start">
                            <div>
                                <h2 class="section-title" style="margin-bottom: 8px;">
                                    {{ $vacancy['title'] }}
                                </h2>
                                <div class="flex items-center gap-2 text-sm text-muted mb-4">
                                    <span style="font-weight: 500; color: var(--text);">
                                        {{ $employerProfiles[$vacancy['employer_id']]['company_name'] ?? 'Неизвестная компания' }}
                                    </span>
                                    <span>•</span>
                                    <span class="flex items-center gap-2">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"></circle>
                                            <path d="M12 7V12L15 14" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                                        </svg>
                                        {{ \Carbon\Carbon::parse($vacancy['created_at'])->diffForHumans() }}
                                    </span>
                                </div>
                            </div>

                            <div class="text-right">
                                <div class="text-xl">
                                    {{ number_format($vacancy['budget'], 0, '.', ' ') }} {{ $vacancy['currency'] }}
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-2 mb-4" style="flex-wrap: wrap;">
                            <span class="badge badge-primary">{{ $vacancy['specialization'] }}</span>
                            <span class="badge badge-neutral">{{ $vacancy['required_experience'] }}</span>

                            @foreach(collect($vacancy['required_skills'])->take(3) as $skill)
                                <span class="badge badge-neutral">{{ $skill }}</span>
                            @endforeach

                            @if(count($vacancy['required_skills']) > 3)
                                <span class="badge badge-neutral">+{{ count($vacancy['required_skills']) - 3 }} еще</span>
                            @endif
                        </div>

                        <p class="text-sm text-muted vacancy-description">{{ $vacancy['description'] }}</p>
                    </a>
                @endforeach
            @endif
        </div>
    </main>
</div>
</body>
</html>
