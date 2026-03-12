<?php

namespace App\Http\Requests\Employer;

use Illuminate\Foundation\Http\FormRequest;

class StoreVacancyRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:150'],
            'specialization' => ['required', 'string', 'max:120'],
            'requiredSkills' => ['nullable', 'string', 'max:500'],
            'requiredExperience' => ['required', 'in:Junior,Middle,Senior,Lead'],
            'description' => ['required', 'string', 'max:3000'],
            'budget' => ['required', 'integer', 'min:0'],
            'currency' => ['required', 'in:RUB,USD,EUR'],
            'status' => ['required', 'in:draft,open'],
        ];
    }

    /**
     * @return list<string>
     */
    public function parsedSkills(): array
    {
        return collect(explode(',', (string) $this->validated('requiredSkills', '')))
            ->map(static fn (string $skill): string => trim($skill))
            ->filter()
            ->unique()
            ->take(20)
            ->values()
            ->all();
    }
}
