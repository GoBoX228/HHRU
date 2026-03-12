<?php

namespace App\Http\Controllers;

use App\Models\Vacancy;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HomePageController extends Controller
{
    public function __invoke(Request $request): View
    {
        Carbon::setLocale('ru');

        $searchTerm = trim((string) $request->query('search', ''));
        $specialization = trim((string) $request->query('specialization', ''));

        $query = Vacancy::query()
            ->with('employerProfile')
            ->where('status', 'open')
            ->whereDoesntHave('applications', static fn (Builder $applicationsQuery) => $applicationsQuery
                ->where('status', 'accepted'));

        if ($searchTerm !== '') {
            $query->where(function ($inner) use ($searchTerm): void {
                $inner
                    ->where('title', 'like', "%{$searchTerm}%")
                    ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        if ($specialization !== '') {
            $query->whereRaw('LOWER(TRIM(specialization)) = ?', [mb_strtolower($specialization)]);
        }

        $activeVacancies = $query
            ->orderByDesc('created_at')
            ->get();

        $uniqueSpecializations = Vacancy::query()
            ->where('status', 'open')
            ->whereDoesntHave('applications', static fn (Builder $applicationsQuery) => $applicationsQuery
                ->where('status', 'accepted'))
            ->whereNotNull('specialization')
            ->where('specialization', '!=', '')
            ->distinct()
            ->orderBy('specialization')
            ->pluck('specialization');

        return view('home', [
            'searchTerm' => $searchTerm,
            'specialization' => $specialization,
            'activeVacancies' => $activeVacancies,
            'uniqueSpecializations' => $uniqueSpecializations,
            'currentUser' => Auth::user(),
        ]);
    }
}
