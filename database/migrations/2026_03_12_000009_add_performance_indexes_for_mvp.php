<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->index(['role', 'is_blocked'], 'users_role_blocked_index');
        });

        Schema::table('vacancies', function (Blueprint $table): void {
            $table->index(['employer_user_id', 'status'], 'vacancies_employer_status_index');
            $table->index(['status', 'created_at'], 'vacancies_status_created_index');
            $table->index(['specialization', 'status'], 'vacancies_specialization_status_index');
        });

        Schema::table('applications', function (Blueprint $table): void {
            $table->index(['freelancer_user_id', 'created_at'], 'applications_freelancer_created_index');
            $table->index(['vacancy_id', 'status'], 'applications_vacancy_status_index');
        });

        Schema::table('chats', function (Blueprint $table): void {
            $table->index(['employer_user_id', 'created_at'], 'chats_employer_created_index');
            $table->index(['freelancer_user_id', 'created_at'], 'chats_freelancer_created_index');
        });

        Schema::table('messages', function (Blueprint $table): void {
            $table->index(['chat_id', 'created_at'], 'messages_chat_created_index');
            $table->index(['sender_user_id', 'created_at'], 'messages_sender_created_index');
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table): void {
            $table->dropIndex('messages_sender_created_index');
            $table->dropIndex('messages_chat_created_index');
        });

        Schema::table('chats', function (Blueprint $table): void {
            $table->dropIndex('chats_freelancer_created_index');
            $table->dropIndex('chats_employer_created_index');
        });

        Schema::table('applications', function (Blueprint $table): void {
            $table->dropIndex('applications_vacancy_status_index');
            $table->dropIndex('applications_freelancer_created_index');
        });

        Schema::table('vacancies', function (Blueprint $table): void {
            $table->dropIndex('vacancies_specialization_status_index');
            $table->dropIndex('vacancies_status_created_index');
            $table->dropIndex('vacancies_employer_status_index');
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->dropIndex('users_role_blocked_index');
        });
    }
};