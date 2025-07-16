<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\User;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;

class CreateUserData extends Data
{
    public function __construct(
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class, 'email')],
            'password' => ['required', 'confirmed'],
            'password_confirmation' => ['required'],
        ];
    }
}
