<?php

namespace App\Http\Controllers;

use App\Http\Requests\Employer\StoreVacancyRequest;
use App\Http\Requests\Employer\UpdateVacancyStatusRequest;
use App\Models\Vacancy;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EmployerDashboardController extends Controller
{
    public function index(): View
    {
        Carbon::setLocale('ru');

        $currentUser = auth()->user();

        $myVacancies = Vacancy::query()
            ->where('employer_user_id', $currentUser->id)
            ->withCount([
                'applications',
                'applications as pending_applications_count' => static fn ($query) => $query
                    ->where('status', 'pending'),
            ])
            ->latest()
            ->get();

        return view('employer.dashboard', [
            'currentUser' => $currentUser,
            'canAccess' => true,
            'myVacancies' => $myVacancies,
        ]);
    }

    public function store(StoreVacancyRequest $request): RedirectResponse
    {
        $currentUser = auth()->user();
        $validated = $request->validated();

        Vacancy::query()->create([
            'employer_user_id' => $currentUser->id,
            'title' => $validated['title'],
            'specialization' => $validated['specialization'],
            'required_skills' => $request->parsedSkills(),
            'required_experience' => $validated['requiredExperience'],
            'description' => $validated['description'],
            'budget' => (int) $validated['budget'],
            'currency' => $validated['currency'],
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('employer.dashboard')
            ->with('success', 'Вакансия создана.');
    }

    public function updateStatus(UpdateVacancyStatusRequest $request, string $id): RedirectResponse
    {
        $currentUser = auth()->user();

        $vacancy = Vacancy::query()
            ->where('id', $id)
            ->where('employer_user_id', $currentUser->id)
            ->first();

        if (! $vacancy) {
            return back()->with('error', 'Вакансия не найдена.');
        }

        $this->authorize('updateStatus', $vacancy);

        $vacancy->update([
            'status' => $request->validated('status'),
        ]);

        return back()->with('success', 'Статус вакансии обновлен.');
    }
}
