<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomePageController extends Controller
{
    public function __invoke(Request $request): View
    {
        Carbon::setLocale('ru');

        $vacancies = collect([
            [
                'id' => 'vac-1',
                'title' => 'Frontend-разработчик (React)',
                'description' => 'Ищем разработчика для создания SPA-платформы с личными кабинетами и интеграцией с API.',
                'specialization' => 'Frontend',
                'required_experience' => 'Middle',
                'required_skills' => ['React', 'TypeScript', 'REST API', 'CSS'],
                'budget' => 180000,
                'currency' => 'RUB',
                'employer_id' => 'emp-1',
                'status' => 'open',
                'created_at' => now()->subHours(6)->toDateTimeString(),
            ],
            [
                'id' => 'vac-2',
                'title' => 'Backend-разработчик (Laravel)',
                'description' => 'Нужен Laravel-разработчик для high-load API, очередей, уведомлений и интеграции платежей.',
                'specialization' => 'Backend',
                'required_experience' => 'Senior',
                'required_skills' => ['Laravel', 'MySQL', 'Redis', 'Docker', 'CI/CD'],
                'budget' => 260000,
                'currency' => 'RUB',
                'employer_id' => 'emp-2',
                'status' => 'open',
                'created_at' => now()->subDays(1)->toDateTimeString(),
            ],
            [
                'id' => 'vac-3',
                'title' => 'UI/UX дизайнер',
                'description' => 'Проектирование интерфейсов и дизайн-системы для B2B сервиса, подготовка макетов и прототипов.',
                'specialization' => 'Design',
                'required_experience' => 'Middle+',
                'required_skills' => ['Figma', 'UI Kit', 'UX Research'],
                'budget' => 140000,
                'currency' => 'RUB',
                'employer_id' => 'emp-3',
                'status' => 'open',
                'created_at' => now()->subDays(2)->toDateTimeString(),
            ],
            [
                'id' => 'vac-4',
                'title' => 'DevOps инженер',
                'description' => 'Настройка инфраструктуры, мониторинга и процессов релиза для распределенной команды.',
                'specialization' => 'DevOps',
                'required_experience' => 'Senior',
                'required_skills' => ['Kubernetes', 'AWS', 'Terraform', 'Prometheus'],
                'budget' => 300000,
                'currency' => 'RUB',
                'employer_id' => 'emp-4',
                'status' => 'closed',
                'created_at' => now()->subDays(5)->toDateTimeString(),
            ],
        ]);

        $employerProfiles = [
            'emp-1' => ['company_name' => 'ООО ВебФокус'],
            'emp-2' => ['company_name' => 'АО ФинТех Системы'],
            'emp-3' => ['company_name' => 'Студия PixelCraft'],
            'emp-4' => ['company_name' => 'InfraCloud'],
        ];

        $searchTerm = trim((string) $request->query('search', ''));
        $specialization = trim((string) $request->query('specialization', ''));

        $activeVacancies = $vacancies
            ->filter(fn (array $vacancy) => $vacancy['status'] === 'open')
            ->filter(function (array $vacancy) use ($searchTerm): bool {
                if ($searchTerm === '') {
                    return true;
                }

                return str_contains(mb_strtolower($vacancy['title']), mb_strtolower($searchTerm))
                    || str_contains(mb_strtolower($vacancy['description']), mb_strtolower($searchTerm));
            })
            ->filter(fn (array $vacancy) => $specialization !== '' ? $vacancy['specialization'] === $specialization : true)
            ->sortByDesc('created_at')
            ->values();

        $uniqueSpecializations = $vacancies
            ->pluck('specialization')
            ->unique()
            ->values();

        return view('home', [
            'searchTerm' => $searchTerm,
            'specialization' => $specialization,
            'activeVacancies' => $activeVacancies,
            'uniqueSpecializations' => $uniqueSpecializations,
            'employerProfiles' => $employerProfiles,
        ]);
    }
}
