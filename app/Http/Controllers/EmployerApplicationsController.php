<?php

namespace App\Http\Controllers;

use App\Support\DemoDataStore;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployerApplicationsController extends Controller
{
    public function __construct(private readonly DemoDataStore $store)
    {
    }

    public function index(Request $request, string $id): View
    {
        Carbon::setLocale('ru');

        $state = $this->store->getState($request);
        $currentUser = $this->store->getCurrentUser($request, $state);

        $vacancy = null;
        if ($currentUser && $currentUser['role'] === 'employer') {
            $vacancy = collect($state['vacancies'])->first(
                fn (array $row) => $row['id'] === $id && $row['employerId'] === $currentUser['id']
            );
        }

        $vacancyApps = collect($state['applications'])
            ->filter(fn (array $application) => $vacancy && $application['vacancyId'] === $vacancy['id'])
            ->sortByDesc('createdAt')
            ->values();

        $usersById = collect($state['users'])->keyBy('id');
        $profiles = $state['freelancerProfiles'];

        return view('employer.applications', [
            'currentUser' => $currentUser,
            'vacancy' => $vacancy,
            'vacancyApps' => $vacancyApps,
            'usersById' => $usersById,
            'profiles' => $profiles,
            'users' => $usersById,
        ]);
    }

    public function updateStatus(Request $request, string $vacancyId, string $applicationId): RedirectResponse
    {
        $state = $this->store->getState($request);
        $currentUser = $this->store->getCurrentUser($request, $state);
        if (!$currentUser || $currentUser['role'] !== 'employer') {
            return back()->with('error', 'Доступ запрещен. Только для работодателей.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:accepted,rejected'],
        ]);

        $vacancy = collect($state['vacancies'])->first(
            fn (array $row) => $row['id'] === $vacancyId && $row['employerId'] === $currentUser['id']
        );
        if (!$vacancy) {
            return back()->with('error', 'Вакансия не найдена.');
        }

        $application = collect($state['applications'])->first(
            fn (array $row) => $row['id'] === $applicationId && $row['vacancyId'] === $vacancyId
        );
        if (!$application) {
            return back()->with('error', 'Отклик не найден.');
        }

        $this->store->updateApplicationStatus($request, $applicationId, $validated['status']);

        return back()->with(
            'success',
            $validated['status'] === 'accepted'
                ? 'Кандидат принят. Вакансия закрыта, остальные отклики отклонены.'
                : 'Отклик отклонен.'
        );
    }
}
