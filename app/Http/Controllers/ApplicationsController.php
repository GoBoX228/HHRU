<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ApplicationsController extends Controller
{
    public function __invoke(): View
    {
        Carbon::setLocale('ru');

        $currentUser = Auth::user();
        $canAccess = $currentUser && $currentUser->role === 'freelancer';

        $myApplications = collect();

        if ($canAccess) {
            $myApplications = Application::query()
                ->with([
                    'vacancy.employerProfile',
                    'vacancy.chats' => static fn ($query) => $query
                        ->where('freelancer_user_id', $currentUser->id),
                ])
                ->where('freelancer_user_id', $currentUser->id)
                ->latest()
                ->get();
        }

        return view('applications', [
            'currentUser' => $currentUser,
            'canAccess' => $canAccess,
            'myApplications' => $myApplications,
        ]);
    }
}
