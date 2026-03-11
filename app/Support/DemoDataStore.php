<?php

namespace App\Support;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DemoDataStore
{
    private const STATE_KEY = 'demo_store_state';
    private const USER_KEY = 'demo_store_user_id';

    public function getState(Request $request): array
    {
        $state = $request->session()->get(self::STATE_KEY);

        if (!is_array($state)) {
            $state = $this->defaultState();
            $request->session()->put(self::STATE_KEY, $state);
        }

        return $state;
    }

    public function saveState(Request $request, array $state): void
    {
        $request->session()->put(self::STATE_KEY, $state);
    }

    public function getCurrentUser(Request $request, ?array $state = null): ?array
    {
        $state ??= $this->getState($request);
        $userId = $request->session()->get(self::USER_KEY, '3');

        if ($userId === null) {
            return null;
        }

        foreach ($state['users'] as $user) {
            if ($user['id'] === $userId) {
                return $user;
            }
        }

        return null;
    }

    public function setCurrentUser(Request $request, ?string $userId): void
    {
        $state = $this->getState($request);

        if ($userId === null) {
            $request->session()->put(self::USER_KEY, null);

            return;
        }

        $exists = collect($state['users'])->contains(fn (array $user) => $user['id'] === $userId);
        if ($exists) {
            $request->session()->put(self::USER_KEY, $userId);
        }
    }

    public function createVacancy(Request $request, array $payload): array
    {
        $state = $this->getState($request);
        array_unshift($state['vacancies'], [
            'id' => Str::lower(Str::random(9)),
            'employerId' => $payload['employerId'],
            'title' => $payload['title'],
            'specialization' => $payload['specialization'],
            'requiredSkills' => $payload['requiredSkills'],
            'requiredExperience' => $payload['requiredExperience'],
            'description' => $payload['description'],
            'budget' => (int) $payload['budget'],
            'currency' => $payload['currency'],
            'status' => $payload['status'],
            'createdAt' => now()->toIso8601String(),
        ]);
        $this->saveState($request, $state);

        return $state;
    }

    public function updateVacancyStatus(Request $request, string $vacancyId, string $status): array
    {
        $state = $this->getState($request);

        foreach ($state['vacancies'] as $index => $vacancy) {
            if ($vacancy['id'] === $vacancyId) {
                $state['vacancies'][$index]['status'] = $status;
                break;
            }
        }

        $this->saveState($request, $state);

        return $state;
    }

    public function applyToVacancy(Request $request, array $payload): array
    {
        $state = $this->getState($request);

        $alreadyApplied = collect($state['applications'])->contains(
            fn (array $app) => $app['vacancyId'] === $payload['vacancyId'] && $app['freelancerId'] === $payload['freelancerId']
        );

        if (!$alreadyApplied) {
            array_unshift($state['applications'], [
                'id' => Str::lower(Str::random(9)),
                'vacancyId' => $payload['vacancyId'],
                'freelancerId' => $payload['freelancerId'],
                'coverLetter' => $payload['coverLetter'],
                'status' => 'pending',
                'createdAt' => now()->toIso8601String(),
            ]);
            $this->saveState($request, $state);
        }

        return $state;
    }

    public function updateApplicationStatus(Request $request, string $applicationId, string $status): array
    {
        $state = $this->getState($request);

        $applicationIndex = collect($state['applications'])->search(
            fn (array $application) => $application['id'] === $applicationId
        );

        if ($applicationIndex === false) {
            return $state;
        }

        $application = $state['applications'][$applicationIndex];
        $state['applications'][$applicationIndex]['status'] = $status;

        if ($status === 'accepted') {
            $vacancyIndex = collect($state['vacancies'])->search(
                fn (array $vacancy) => $vacancy['id'] === $application['vacancyId']
            );

            if ($vacancyIndex !== false) {
                $vacancy = $state['vacancies'][$vacancyIndex];
                $state['vacancies'][$vacancyIndex]['status'] = 'closed';

                foreach ($state['applications'] as $index => $row) {
                    if ($row['vacancyId'] === $vacancy['id'] && $row['id'] !== $applicationId) {
                        $state['applications'][$index]['status'] = 'rejected';
                    }
                }

                $chatExists = collect($state['chats'])->contains(
                    fn (array $chat) => $chat['vacancyId'] === $vacancy['id'] && $chat['freelancerId'] === $application['freelancerId']
                );

                if (!$chatExists) {
                    $state['chats'][] = [
                        'id' => 'chat_' . $applicationId,
                        'vacancyId' => $vacancy['id'],
                        'employerId' => $vacancy['employerId'],
                        'freelancerId' => $application['freelancerId'],
                        'createdAt' => now()->toIso8601String(),
                    ];
                }
            }
        }

        $this->saveState($request, $state);

        return $state;
    }

    private function defaultState(): array
    {
        Carbon::setLocale('ru');

        return [
            'users' => [
                ['id' => '1', 'role' => 'admin', 'email' => 'admin@test.com', 'name' => 'Администратор', 'isBlocked' => false],
                ['id' => '2', 'role' => 'employer', 'email' => 'employer@test.com', 'name' => 'Тех Корп', 'isBlocked' => false],
                ['id' => '3', 'role' => 'freelancer', 'email' => 'freelancer@test.com', 'name' => 'Иван Иванов', 'isBlocked' => false],
            ],
            'freelancerProfiles' => [
                '3' => [
                    'userId' => '3',
                    'specialization' => 'Frontend разработчик',
                    'skills' => ['React', 'TypeScript', 'CSS'],
                    'experience' => 'Middle',
                    'about' => 'Увлеченный frontend-разработчик с опытом создания SPA и корпоративных интерфейсов.',
                    'birthDate' => '1990-01-01',
                    'gender' => 'male',
                ],
            ],
            'employerProfiles' => [
                '2' => [
                    'userId' => '2',
                    'companyName' => 'Тех Корп',
                    'companyDescription' => 'Ведущая IT-компания, которая создает SaaS-продукты для бизнеса.',
                    'companyField' => 'IT',
                ],
            ],
            'vacancies' => [
                [
                    'id' => 'vac-1',
                    'employerId' => '2',
                    'title' => 'Senior React разработчик',
                    'specialization' => 'Frontend',
                    'requiredSkills' => ['React', 'TypeScript', 'Zustand'],
                    'requiredExperience' => 'Senior',
                    'description' => 'Ищем Senior React разработчика в продуктовую команду для развития клиентской платформы.',
                    'budget' => 5000,
                    'currency' => 'USD',
                    'status' => 'open',
                    'createdAt' => now()->subHours(4)->toIso8601String(),
                ],
                [
                    'id' => 'vac-2',
                    'employerId' => '2',
                    'title' => 'Backend Laravel разработчик',
                    'specialization' => 'Backend',
                    'requiredSkills' => ['Laravel', 'PHP', 'PostgreSQL'],
                    'requiredExperience' => 'Middle',
                    'description' => 'Нужен надежный backend-разработчик для нового MVP с высокими требованиями к качеству кода.',
                    'budget' => 3000,
                    'currency' => 'USD',
                    'status' => 'draft',
                    'createdAt' => now()->subDay()->toIso8601String(),
                ],
            ],
            'applications' => [
                [
                    'id' => 'a1',
                    'vacancyId' => 'vac-1',
                    'freelancerId' => '3',
                    'coverLetter' => 'Здравствуйте! У меня 5+ лет опыта в React и TypeScript. Буду рад обсудить проект и задачи.',
                    'status' => 'pending',
                    'createdAt' => now()->subHours(2)->toIso8601String(),
                ],
            ],
            'chats' => [],
            'messages' => [],
        ];
    }
}
