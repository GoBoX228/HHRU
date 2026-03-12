<?php

namespace App\Http\Controllers;

use App\Http\Requests\Employer\UpdateApplicationStatusRequest;
use App\Models\Application;
use App\Models\Chat;
use App\Models\User;
use App\Models\Vacancy;
use App\Notifications\ApplicationAcceptedNotification;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class EmployerApplicationsController extends Controller
{
    public function index(string $id): View
    {
        Carbon::setLocale('ru');

        $currentUser = auth()->user();

        $vacancy = Vacancy::query()
            ->where('id', $id)
            ->where('employer_user_id', $currentUser->id)
            ->first();

        if (! $vacancy) {
            abort(404);
        }

        $this->authorize('manageApplications', $vacancy);

        $vacancyApps = Application::query()
            ->with(['freelancer.freelancerProfile'])
            ->where('vacancy_id', $vacancy->id)
            ->latest()
            ->get();

        return view('employer.applications', [
            'currentUser' => $currentUser,
            'canAccess' => true,
            'vacancy' => $vacancy,
            'vacancyApps' => $vacancyApps,
        ]);
    }

    public function updateStatus(UpdateApplicationStatusRequest $request, string $vacancyId, string $applicationId): RedirectResponse
    {
        $currentUser = auth()->user();

        $vacancy = Vacancy::query()
            ->where('id', $vacancyId)
            ->where('employer_user_id', $currentUser->id)
            ->first();

        if (! $vacancy) {
            return back()->with('error', 'Вакансия не найдена.');
        }

        $this->authorize('manageApplications', $vacancy);

        $application = Application::query()
            ->where('id', $applicationId)
            ->where('vacancy_id', $vacancy->id)
            ->first();

        if (! $application) {
            return back()->with('error', 'Отклик не найден.');
        }

        if ($application->status !== 'pending') {
            return back()->with('error', 'Статус этого отклика уже изменен.');
        }

        $status = $request->validated('status');
        $acceptedChat = null;

        DB::transaction(function () use ($status, $application, $vacancy, &$acceptedChat): void {
            $application->update([
                'status' => $status,
            ]);

            if ($status !== 'accepted') {
                return;
            }

            $vacancy->update([
                'status' => 'closed',
            ]);

            Application::query()
                ->where('vacancy_id', $vacancy->id)
                ->where('id', '!=', $application->id)
                ->where('status', 'pending')
                ->update(['status' => 'rejected']);

            $acceptedChat = Chat::query()->firstOrCreate([
                'vacancy_id' => $vacancy->id,
                'employer_user_id' => $vacancy->employer_user_id,
                'freelancer_user_id' => $application->freelancer_user_id,
            ]);
        });

        if ($status === 'accepted') {
            $freelancer = User::query()->find((int) $application->freelancer_user_id);

            if ($freelancer && ! $freelancer->is_blocked) {
                $freelancer->notify(new ApplicationAcceptedNotification(
                    vacancyTitle: (string) $vacancy->title,
                    chatUrl: route('chat.show', ['chat' => $acceptedChat?->id]),
                ));
            }
        }

        return back()->with(
            'success',
            $status === 'accepted'
                ? 'Кандидат принят. Вакансия закрыта, остальные отклики отклонены.'
                : 'Отклик отклонен.'
        );
    }
}