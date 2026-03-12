<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\EmployerProfile;
use App\Models\FreelancerProfile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->validated();

        $user = User::query()->where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password) || $user->is_blocked) {
            return back()
                ->withInput(['email' => $credentials['email']])
                ->with('auth_error', 'Неверные учетные данные или аккаунт заблокирован.');
        }

        Auth::login($user, (bool) ($credentials['remember'] ?? false));
        $request->session()->regenerate();

        return $this->redirectAfterAuth($user);
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $user = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => $data['role'],
            'phone' => $data['phone'],
            'is_blocked' => false,
        ]);

        if ($data['role'] === 'employer') {
            EmployerProfile::query()->create([
                'user_id' => $user->id,
                'company_name' => $data['company_name'] ?? '',
                'company_field' => $data['company_field'] ?? null,
            ]);
        }

        if ($data['role'] === 'freelancer') {
            FreelancerProfile::query()->create([
                'user_id' => $user->id,
                'skills' => [],
                'gender' => 'other',
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return $this->redirectAfterAuth($user);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function redirectAfterAuth(User $user): RedirectResponse
    {
        return match ($user->role) {
            'employer' => redirect()->route('employer.dashboard'),
            'admin' => redirect()->route('admin.dashboard'),
            default => redirect()->route('profile'),
        };
    }
}
