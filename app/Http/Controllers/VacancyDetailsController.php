<?php

namespace App\Http\Controllers;

use App\Support\DemoDataStore;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VacancyDetailsController extends Controller
{
    public function __construct(private readonly DemoDataStore $store)
    {
    }

    public function show(Request $request, string $id): View
    {
        Carbon::setLocale('ru');

        $state = $this->store->getState($request);
        $currentUser = $this->store->getCurrentUser($request, $state);

        $vacancy = collect($state['vacancies'])->first(fn (array $row) => $row['id'] === $id);
        $employer = $vacancy ? ($state['employerProfiles'][$vacancy['employerId']] ?? null) : null;

        $existingApplication = null;
        if ($currentUser && $currentUser['role'] === 'freelancer') {
            $existingApplication = collect($state['applications'])->first(
                fn (array $row) => $row['vacancyId'] === $id && $row['freelancerId'] === $currentUser['id']
            );
        }

        return view('vacancies.show', [
            'currentUser' => $currentUser,
            'vacancy' => $vacancy,
            'employer' => $employer,
            'existingApplication' => $existingApplication,
            'users' => collect($state['users'])->keyBy('id'),
        ]);
    }

    public function apply(Request $request, string $id): RedirectResponse
    {
        $state = $this->store->getState($request);
        $currentUser = $this->store->getCurrentUser($request, $state);
        if (!$currentUser || $currentUser['role'] !== 'freelancer') {
            return back()->with('error', 'Откликаться могут только фрилансеры.');
        }

        $vacancy = collect($state['vacancies'])->first(fn (array $row) => $row['id'] === $id);
        if (!$vacancy) {
            return back()->with('error', 'Вакансия не найдена.');
        }

        if ($vacancy['status'] !== 'open') {
            return back()->with('error', 'Эта вакансия уже закрыта для откликов.');
        }

        $validated = $request->validate([
            'coverLetter' => ['required', 'string', 'max:1000'],
        ]);

        $this->store->applyToVacancy($request, [
            'vacancyId' => $id,
            'freelancerId' => $currentUser['id'],
            'coverLetter' => $validated['coverLetter'],
        ]);

        return redirect()
            ->route('vacancies.show', ['id' => $id])
            ->with('success', 'Отклик отправлен.');
    }
}
