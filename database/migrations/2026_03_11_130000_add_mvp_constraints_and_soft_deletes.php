<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->softDeletes();
            $table->unique('phone');
        });

        Schema::table('vacancies', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('applications', function (Blueprint $table): void {
            $table->unique(['vacancy_id', 'freelancer_user_id'], 'applications_vacancy_freelancer_unique');
        });

        Schema::table('chats', function (Blueprint $table): void {
            $table->unique(['vacancy_id', 'freelancer_user_id'], 'chats_vacancy_freelancer_unique');
        });
    }

    public function down(): void
    {
        Schema::table('chats', function (Blueprint $table): void {
            $table->dropUnique('chats_vacancy_freelancer_unique');
        });

        Schema::table('applications', function (Blueprint $table): void {
            $table->dropUnique('applications_vacancy_freelancer_unique');
        });

        Schema::table('vacancies', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->dropUnique(['phone']);
            $table->dropSoftDeletes();
        });
    }
};