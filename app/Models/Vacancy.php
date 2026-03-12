<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vacancy extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employer_user_id',
        'title',
        'specialization',
        'required_experience',
        'required_skills',
        'description',
        'budget',
        'currency',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'required_skills' => 'array',
            'budget' => 'integer',
        ];
    }

    public function employer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employer_user_id');
    }

    public function employerProfile(): BelongsTo
    {
        return $this->belongsTo(EmployerProfile::class, 'employer_user_id', 'user_id');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function chats(): HasMany
    {
        return $this->hasMany(Chat::class);
    }
}
