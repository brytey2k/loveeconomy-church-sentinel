<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\User;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\FromRouteParameter;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;

class UpdateUserData extends Data
{
    public function __construct(
        #[FromRouteParameter('user')]
        public User $user,
        public string $name,
        public string $email,
        public string|null $password = null,
        public string|null $password_confirmation = null,
    ) {
    }

    /**
     * @param ValidationContext|null $context
     *
     * @return array<string, list<\Illuminate\Validation\Rules\Unique|string>>
     */
    public static function rules(ValidationContext|null $context): array
    {
        return [
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255', Rule::unique(User::class, 'email')->ignore($context?->payload['user']->id ?? 0)],
            'password' => ['nullable', 'confirmed'],
            'password_confirmation' => ['nullable', 'required_with:password'],
        ];
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        $data = parent::toArray();

        if (empty($this->password)) {
            unset($data['password'], $data['password_confirmation']);
        }

        return $data;
    }
}
