<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\User;
use App\Models\Vacancy;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(Request $request): View
    {
        Carbon::setLocale('ru');

        $currentUser = auth()->user();
        $activeTab = in_array((string) $request->query('tab'), ['users', 'vacancies'], true)
            ? (string) $request->query('tab')
            : 'users';

        $users = User::query()->orderBy('id')->get();
        $vacancies = Vacancy::query()
            ->with('employer')
            ->orderByDesc('created_at')
            ->get();

        $totalApplications = Schema::hasTable('applications') ? Application::query()->count() : 0;

        $stats = [
            'total_users' => $users->count(),
            'freelancers' => $users->where('role', 'freelancer')->count(),
            'employers' => $users->where('role', 'employer')->count(),
            'blocked_users' => $users->where('is_blocked', true)->count(),
            'total_vacancies' => $vacancies->count(),
            'active_vacancies' => $vacancies->where('status', 'open')->count(),
            'total_applications' => $totalApplications,
        ];

        return view('admin-dashboard', [
            'currentUser' => $currentUser,
            'canAccess' => true,
            'activeTab' => $activeTab,
            'users' => $users,
            'vacancies' => $vacancies,
            'stats' => $stats,
        ]);
    }

    public function toggleUserBlock(User $user): RedirectResponse
    {
        $this->authorize('toggleBlock', $user);

        $user->update([
            'is_blocked' => ! $user->is_blocked,
        ]);

        return back();
    }

    public function archiveVacancy(Vacancy $vacancy): RedirectResponse
    {
        $this->authorize('archive', $vacancy);

        if ($vacancy->status !== 'archived') {
            $vacancy->update([
                'status' => 'archived',
            ]);
        }

        return back();
    }
}
