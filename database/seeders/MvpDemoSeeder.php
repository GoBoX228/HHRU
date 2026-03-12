<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Chat;
use App\Models\EmployerProfile;
use App\Models\FreelancerProfile;
use App\Models\Message;
use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Database\Seeder;

class MvpDemoSeeder extends Seeder
{
    public function run(): void
    {
        $specializations = [
            'Frontend Development',
            'Backend Development',
            'Mobile Development',
            'DevOps',
            'QA Automation',
        ];

        $experiences = ['Junior', 'Middle', 'Senior', 'Lead', 'Middle'];

        $skillsMap = [
            ['HTML', 'CSS', 'JavaScript'],
            ['PHP', 'Laravel', 'MySQL'],
            ['React Native', 'TypeScript', 'API'],
            ['Docker', 'CI/CD', 'Linux'],
            ['Selenium', 'Postman', 'SQL'],
        ];

        $employers = collect(range(1, 5))->map(function (int $index): User {
            return User::query()->updateOrCreate(
                ['email' => "employer{$index}@seed.local"],
                [
                    'name' => "Employer {$index}",
                    'password' => 'password',
                    'role' => 'employer',
                    'phone' => sprintf('+79001000%03d', $index),
                    'is_blocked' => false,
                    'email_verified_at' => now(),
                ]
            );
        })->values();

        $freelancers = collect(range(1, 5))->map(function (int $index): User {
            return User::query()->updateOrCreate(
                ['email' => "freelancer{$index}@seed.local"],
                [
                    'name' => "Freelancer {$index}",
                    'password' => 'password',
                    'role' => 'freelancer',
                    'phone' => sprintf('+79002000%03d', $index),
                    'is_blocked' => false,
                    'email_verified_at' => now(),
                ]
            );
        })->values();

        User::query()->updateOrCreate(
            ['email' => 'admin@seed.local'],
            [
                'name' => 'Admin Seed',
                'password' => 'password',
                'role' => 'admin',
                'phone' => '+79003000001',
                'is_blocked' => false,
                'email_verified_at' => now(),
            ]
        );

        foreach ($employers as $index => $user) {
            EmployerProfile::query()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'company_name' => 'Seed Company '.($index + 1),
                    'company_description' => 'Company profile for demo vacancies and applications.',
                    'company_field' => $specializations[$index],
                ]
            );
        }

        foreach ($freelancers as $index => $user) {
            FreelancerProfile::query()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'specialization' => $specializations[$index],
                    'skills' => $skillsMap[$index],
                    'experience' => $experiences[$index],
                    'about' => 'Demo freelancer profile #'.($index + 1).'.',
                    'birth_date' => now()->subYears(22 + $index)->toDateString(),
                    'gender' => ['male', 'female', 'other'][$index % 3],
                ]
            );
        }

        $vacancies = collect(range(1, 5))->map(function (int $index) use (
            $employers,
            $specializations,
            $experiences,
            $skillsMap
        ): Vacancy {
            return Vacancy::query()->updateOrCreate(
                [
                    'employer_user_id' => $employers[$index - 1]->id,
                    'title' => "Test Vacancy {$index}",
                ],
                [
                    'specialization' => $specializations[$index - 1],
                    'required_experience' => $experiences[$index - 1],
                    'required_skills' => $skillsMap[$index - 1],
                    'description' => "Demo vacancy description #{$index}.",
                    'budget' => 60000 + ($index * 15000),
                    'currency' => ['RUB', 'USD', 'EUR'][($index - 1) % 3],
                    'status' => 'open',
                ]
            );
        })->values();

        $applications = collect(range(1, 5))->map(function (int $index) use ($vacancies, $freelancers): Application {
            return Application::query()->updateOrCreate(
                [
                    'vacancy_id' => $vacancies[$index - 1]->id,
                    'freelancer_user_id' => $freelancers[$index - 1]->id,
                ],
                [
                    'cover_letter' => "I can complete vacancy #{$index}.",
                    'status' => 'pending',
                ]
            );
        })->values();

        $chats = collect(range(1, 5))->map(function (int $index) use ($vacancies, $freelancers, $employers): Chat {
            return Chat::query()->updateOrCreate(
                [
                    'vacancy_id' => $vacancies[$index - 1]->id,
                    'freelancer_user_id' => $freelancers[$index - 1]->id,
                ],
                [
                    'employer_user_id' => $employers[$index - 1]->id,
                ]
            );
        })->values();

        foreach ($chats as $index => $chat) {
            Message::query()->updateOrCreate(
                [
                    'chat_id' => $chat->id,
                    'sender_user_id' => $applications[$index]->freelancer_user_id,
                ],
                [
                    'text' => 'Demo chat message #'.($index + 1).'.',
                ]
            );
        }
    }
}
