<?php

namespace App\Http\Controllers;

use App\Http\Requests\Vacancy\ApplyToVacancyRequest;
use App\Models\Application;
use App\Models\User;
use App\Models\Vacancy;
use App\Notifications\NewApplicationNotification;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class VacancyDetailsController extends Controller
{
    public function show(string $id): View
    {
        Carbon::setLocale('ru');

        $currentUser = auth()->user();
        $vacancy = Vacancy::query()
            ->with('employerProfile')
            ->find($id);

        $existingApplication = null;

        if ($vacancy && $currentUser?->role === 'freelancer') {
            $existingApplication = Application::query()
                ->where('vacancy_id', $vacancy->id)
                ->where('freelancer_user_id', $currentUser->id)
                ->first();
        }

        return view('vacancies.show', [
            'currentUser' => $currentUser,
            'vacancy' => $vacancy,
            'employer' => $vacancy?->employerProfile,
            'existingApplication' => $existingApplication,
        ]);
    }

    public function apply(ApplyToVacancyRequest $request, string $id): RedirectResponse
    {
        $currentUser = auth()->user();

        $vacancy = Vacancy::query()->find($id);

        if (! $vacancy) {
            return back()->with('error', 'Вакансия не найдена.');
        }

        $this->authorize('apply', $vacancy);

        $coverLetter = $request->validated('coverLetter');

        $application = Application::query()->firstOrCreate(
            [
                'vacancy_id' => $vacancy->id,
                'freelancer_user_id' => $currentUser->id,
            ],
            [
                'cover_letter' => $coverLetter,
                'status' => 'pending',
            ]
        );

        if (! $application->wasRecentlyCreated) {
            return back()->with('error', 'Вы уже откликались на эту вакансию.');
        }

        $employer = User::query()->find((int) $vacancy->employer_user_id);

        if ($employer && ! $employer->is_blocked) {
            $employer->notify(new NewApplicationNotification(
                vacancyTitle: (string) $vacancy->title,
                freelancerName: (string) $currentUser->name,
                applicationsUrl: route('employer.applications.index', ['id' => $vacancy->id]),
            ));
        }

        return redirect()
            ->route('vacancies.show', ['id' => $vacancy->id])
            ->with('success', 'Отклик отправлен.');
    }
}