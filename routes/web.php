<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\ApplicationsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ChatsController;
use App\Http\Controllers\DemoUserController;
use App\Http\Controllers\EmployerApplicationsController;
use App\Http\Controllers\EmployerDashboardController;
use App\Http\Controllers\HomePageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VacancyDetailsController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomePageController::class)->name('home');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/profile', ProfileController::class)->name('profile');
    Route::post('/profile', ProfileController::class)->name('profile.update');
    Route::get('/applications', ApplicationsController::class)->name('applications');
    Route::get('/chats', [ChatsController::class, 'index'])->name('chats');
    Route::get('/chats/{chat}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chats/{chat}/messages', [ChatController::class, 'store'])->name('chat.messages.store');

    Route::get('/admin', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::post('/admin/users/{user}/toggle-block', [AdminDashboardController::class, 'toggleUserBlock'])
        ->name('admin.users.toggle-block');
    Route::post('/admin/vacancies/{vacancy}/archive', [AdminDashboardController::class, 'archiveVacancy'])
        ->name('admin.vacancies.archive');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::post('/demo/user/switch', [DemoUserController::class, 'switch'])->name('demo.user.switch');

Route::get('/vacancies/{id}', [VacancyDetailsController::class, 'show'])->name('vacancies.show');
Route::post('/vacancies/{id}/apply', [VacancyDetailsController::class, 'apply'])->name('vacancies.apply');

Route::get('/employer/vacancies', [EmployerDashboardController::class, 'index'])->name('employer.dashboard');
Route::post('/employer/vacancies', [EmployerDashboardController::class, 'store'])->name('employer.vacancies.store');
Route::post('/employer/vacancies/{id}/status', [EmployerDashboardController::class, 'updateStatus'])->name('employer.vacancies.status');

Route::get('/employer/vacancies/{id}/applications', [EmployerApplicationsController::class, 'index'])->name('employer.applications.index');
Route::post('/employer/vacancies/{vacancyId}/applications/{applicationId}/status', [EmployerApplicationsController::class, 'updateStatus'])->name('employer.applications.status');

Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});