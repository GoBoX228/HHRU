<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __invoke(): View|RedirectResponse
    {
        $currentUser = auth()->user();

        if (! $currentUser) {
            return redirect()->route('login');
        }

        return view('profile', [
            'currentUser' => $currentUser,
        ]);
    }
}
