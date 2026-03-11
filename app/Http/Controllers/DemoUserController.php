<?php

namespace App\Http\Controllers;

use App\Support\DemoDataStore;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DemoUserController extends Controller
{
    public function __construct(private readonly DemoDataStore $store)
    {
    }

    public function switch(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'in:guest,1,2,3'],
        ]);

        $this->store->setCurrentUser(
            $request,
            $validated['user_id'] === 'guest' ? null : $validated['user_id']
        );

        return back();
    }
}
