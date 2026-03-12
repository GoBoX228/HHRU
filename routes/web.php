<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\ApplicationsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ChatsController;
use App\Http\Controllers\EmployerApplicationsController;
use App\Http\Controllers\EmployerDashboardController;
use App\Http\Controllers\HomePageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VacancyDetailsController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomePageController::class)->name('home');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:login')
        ->name('login.submit');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware('active')->group(function (): void {
        Route::middleware('role:freelancer')->group(function (): void {
            Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
            Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');

            Route::get('/applications', ApplicationsController::class)->name('applications');
            Route::post('/vacancies/{id}/apply', [VacancyDetailsController::class, 'apply'])->name('vacancies.apply');
        });

        Route::middleware('role:freelancer,employer')->group(function (): void {
            Route::get('/chats', [ChatsController::class, 'index'])->name('chats');
            Route::get('/chats/{chat}', [ChatController::class, 'show'])->name('chat.show');
            Route::get('/chats/{chat}/messages', [ChatController::class, 'messages'])->name('chat.messages.index');
            Route::post('/chats/{chat}/messages', [ChatController::class, 'store'])->name('chat.messages.store');
        });

        Route::middleware('role:employer')->group(function (): void {
            Route::get('/employer/vacancies', [EmployerDashboardController::class, 'index'])->name('employer.dashboard');
            Route::post('/employer/vacancies', [EmployerDashboardController::class, 'store'])->name('employer.vacancies.store');
            Route::post('/employer/vacancies/{id}/status', [EmployerDashboardController::class, 'updateStatus'])
                ->name('employer.vacancies.status');

            Route::get('/employer/vacancies/{id}/applications', [EmployerApplicationsController::class, 'index'])
                ->name('employer.applications.index');
            Route::post('/employer/vacancies/{vacancyId}/applications/{applicationId}/status', [EmployerApplicationsController::class, 'updateStatus'])
                ->name('employer.applications.status');
        });

        Route::middleware('role:admin')->group(function (): void {
            Route::get('/admin', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
            Route::post('/admin/users/{user}/toggle-block', [AdminDashboardController::class, 'toggleUserBlock'])
                ->name('admin.users.toggle-block');
            Route::post('/admin/vacancies/{vacancy}/archive', [AdminDashboardController::class, 'archiveVacancy'])
                ->name('admin.vacancies.archive');
        });
    });
});

Route::get('/vacancies/{id}', [VacancyDetailsController::class, 'show'])->name('vacancies.show');

Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});