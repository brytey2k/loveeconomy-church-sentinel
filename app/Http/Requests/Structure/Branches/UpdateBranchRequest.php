<?php

declare(strict_types=1);

namespace App\Http\Requests\Structure\Branches;

use App\Dto\Structure\Branches\UpdateBranchDto;
use App\Models\Branch;
use App\Models\Country;
use App\Models\Level;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBranchRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'level' => ['required', 'integer', Rule::exists(Level::class, 'id')],
            'country' => ['required', 'integer', Rule::exists(Country::class, 'id')],
            'parent' => ['nullable', 'integer', Rule::exists(Branch::class, 'id')],
        ];
    }

    public function toDto(): UpdateBranchDto
    {
        return new UpdateBranchDto(
            name: $this->string('name')->toString(),
            levelId: $this->integer('level'),
            countryId: $this->integer('country'),
            parentId: $this->integer('parent'),
        );
    }
}
