<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;

class CreateUserData extends Data
{
    public function __construct(
        public int $branch_id,
        public int|null $stationed_branch_id,
        public string $name,
        public string $email,
        public string $password,
        public string $password_confirmation,
    ) {
    }

    /**
     * @return array<string, list<\Illuminate\Validation\Rules\Unique|string>>
     */
    public static function rules(): array
    {
        return [
            'branch_id' => ['required', 'integer', Rule::exists(Branch::class, 'id')],
            'stationed_branch_id' => ['nullable', 'integer', Rule::exists(Branch::class, 'id')],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class, 'email')],
            'password' => ['required', 'confirmed'],
            'password_confirmation' => ['required'],
        ];
    }
}
