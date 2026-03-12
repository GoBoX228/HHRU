<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\UpdateFreelancerProfileRequest;
use App\Models\FreelancerProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(): View
    {
        $currentUser = Auth::user();
        $currentUser->loadMissing('freelancerProfile');

        return view('profile', [
            'currentUser' => $currentUser,
            'profile' => $currentUser->freelancerProfile,
        ]);
    }

    public function update(UpdateFreelancerProfileRequest $request): RedirectResponse
    {
        $currentUser = Auth::user();
        $data = $request->validated();

        FreelancerProfile::query()->updateOrCreate(
            ['user_id' => $currentUser->id],
            [
                'specialization' => $data['specialization'],
                'experience' => $data['experience'] ?: null,
                'birth_date' => $data['birth_date'] ?: null,
                'gender' => $data['gender'] ?: 'other',
                'skills' => $request->parsedSkills(),
                'about' => $data['about'] ?: null,
            ]
        );

        return redirect()
            ->route('profile')
            ->with('profile_saved', true);
    }
}
