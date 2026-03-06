<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vacancies', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('employer_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->string('specialization')->nullable();
            $table->string('required_experience')->nullable();
            $table->json('required_skills')->nullable();
            $table->text('description');
            $table->unsignedInteger('budget')->default(0);
            $table->string('currency', 3)->default('RUB');
            $table->string('status', 32)->default('open')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vacancies');
    }
};
