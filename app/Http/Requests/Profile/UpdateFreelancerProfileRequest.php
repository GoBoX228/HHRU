<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFreelancerProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'specialization' => ['required', 'string', 'max:255'],
            'experience' => ['nullable', 'in:Junior,Middle,Senior,Lead'],
            'birth_date' => ['nullable', 'date'],
            'gender' => ['nullable', 'in:male,female,other'],
            'skills' => ['nullable', 'string', 'max:1000'],
            'about' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * @return list<string>
     */
    public function parsedSkills(): array
    {
        return collect(explode(',', (string) $this->validated('skills', '')))
            ->map(static fn (string $skill): string => trim($skill))
            ->filter()
            ->unique()
            ->take(20)
            ->values()
            ->all();
    }
}
