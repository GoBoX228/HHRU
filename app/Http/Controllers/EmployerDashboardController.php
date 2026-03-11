<?php

namespace App\Http\Controllers;

use App\Support\DemoDataStore;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployerDashboardController extends Controller
{
    public function __construct(private readonly DemoDataStore $store)
    {
    }

    public function index(Request $request): View
    {
        Carbon::setLocale('ru');

        $state = $this->store->getState($request);
        $currentUser = $this->store->getCurrentUser($request, $state);
        $myVacancies = collect($state['vacancies'])
            ->filter(fn (array $vacancy) => $currentUser && $vacancy['employerId'] === $currentUser['id'])
            ->sortByDesc('createdAt')
            ->values();

        $applicationsByVacancy = collect($state['applications'])
            ->groupBy('vacancyId')
            ->map(fn ($apps) => [
                'total' => $apps->count(),
                'pending' => $apps->where('status', 'pending')->count(),
            ]);

        return view('employer.dashboard', [
            'currentUser' => $currentUser,
            'myVacancies' => $myVacancies,
            'applicationsByVacancy' => $applicationsByVacancy,
            'users' => collect($state['users'])->keyBy('id'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $state = $this->store->getState($request);
        $currentUser = $this->store->getCurrentUser($request, $state);
        if (!$currentUser || $currentUser['role'] !== 'employer') {
            return back()->with('error', 'Доступ запрещен. Только для работодателей.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'specialization' => ['required', 'string', 'max:120'],
            'requiredSkills' => ['nullable', 'string', 'max:500'],
            'requiredExperience' => ['required', 'string', 'max:60'],
            'description' => ['required', 'string', 'max:3000'],
            'budget' => ['required', 'integer', 'min:0'],
            'currency' => ['required', 'in:RUB,USD,EUR'],
            'status' => ['required', 'in:draft,open'],
        ]);

        $skills = collect(explode(',', (string) $validated['requiredSkills']))
            ->map(fn (string $skill) => trim($skill))
            ->filter()
            ->take(20)
            ->values()
            ->all();

        $this->store->createVacancy($request, [
            'employerId' => $currentUser['id'],
            'title' => $validated['title'],
            'specialization' => $validated['specialization'],
            'requiredSkills' => $skills,
            'requiredExperience' => $validated['requiredExperience'],
            'description' => $validated['description'],
            'budget' => (int) $validated['budget'],
            'currency' => $validated['currency'],
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('employer.dashboard')
            ->with('success', 'Вакансия создана.');
    }

    public function updateStatus(Request $request, string $id): RedirectResponse
    {
        $state = $this->store->getState($request);
        $currentUser = $this->store->getCurrentUser($request, $state);
        if (!$currentUser || $currentUser['role'] !== 'employer') {
            return back()->with('error', 'Доступ запрещен. Только для работодателей.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:open,archived'],
        ]);

        $vacancy = collect($state['vacancies'])->first(
            fn (array $row) => $row['id'] === $id && $row['employerId'] === $currentUser['id']
        );
        if (!$vacancy) {
            return back()->with('error', 'Вакансия не найдена.');
        }

        $this->store->updateVacancyStatus($request, $id, $validated['status']);

        return back()->with('success', 'Статус вакансии обновлен.');
    }
}
