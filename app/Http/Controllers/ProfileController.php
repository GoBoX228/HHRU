<?php

namespace App\Http\Controllers;

use App\Models\FreelancerProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __invoke(Request $request): View|RedirectResponse
    {
        $currentUser = Auth::user();

        if (! $currentUser) {
            return redirect()->route('login');
        }

        if ($request->isMethod('post')) {
            if ($currentUser->role !== 'freelancer') {
                abort(403);
            }

            $data = $request->validate([
                'specialization' => ['required', 'string', 'max:255'],
                'experience' => ['nullable', 'in:Junior,Middle,Senior,Lead'],
                'birth_date' => ['nullable', 'date'],
                'gender' => ['nullable', 'in:male,female,other'],
                'skills' => ['nullable', 'string', 'max:1000'],
                'about' => ['nullable', 'string', 'max:1000'],
            ]);

            $skills = collect(explode(',', (string) ($data['skills'] ?? '')))
                ->map(static fn (string $skill): string => trim($skill))
                ->filter()
                ->unique()
                ->take(20)
                ->values()
                ->all();

            FreelancerProfile::query()->updateOrCreate(
                ['user_id' => $currentUser->id],
                [
                    'specialization' => $data['specialization'],
                    'experience' => $data['experience'] ?: null,
                    'birth_date' => $data['birth_date'] ?: null,
                    'gender' => $data['gender'] ?: 'other',
                    'skills' => $skills,
                    'about' => $data['about'] ?: null,
                ]
            );

            return redirect()
                ->route('profile')
                ->with('profile_saved', true);
        }

        $currentUser->loadMissing('freelancerProfile');

        return view('profile', [
            'currentUser' => $currentUser,
            'profile' => $currentUser->freelancerProfile,
        ]);
    }
}
