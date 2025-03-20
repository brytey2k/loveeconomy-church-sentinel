<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Dto\UpdateLevelDto;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateLevelRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'position' => ['required', 'integer', 'min:1'],
        ];
    }

    public function toDto(): UpdateLevelDto
    {
        return new UpdateLevelDto(
            name: $this->string('name')->toString(),
            position: $this->integer('position'),
        );
    }
}
