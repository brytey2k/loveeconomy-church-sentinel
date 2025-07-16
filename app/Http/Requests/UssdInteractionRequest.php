<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Data\Ussd\UssdInteractionRequestData;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UssdInteractionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<int, string|ValidationRule>|string>
     */
    public function rules(): array
    {
        return [
            'Type' => ['nullable', 'string'],
            'Message' => ['nullable', 'string'],
            'ServiceCode' => ['nullable', 'string'],
            'Operator' => ['nullable', 'string'],
            'ClientState' => ['nullable', 'string'],
            'Mobile' => ['nullable', 'string'],
            'SessionId' => ['nullable', 'string'],
            'Sequence' => ['nullable', 'integer'],
            'Platform' => ['nullable', 'string'],
        ];
    }

    public function toDto(): UssdInteractionRequestData
    {
        return new UssdInteractionRequestData(
            type: $this->string('Type')->toString(),
            message: $this->string('Message')->toString(),
            serviceCode: $this->string('ServiceCode')->toString(),
            operator: $this->string('Operator')->toString(),
            clientState: $this->string('ClientState')->toString(),
            mobile: $this->string('Mobile')->toString(),
            sessionId: $this->string('SessionId')->toString(),
            sequence: $this->integer('Sequence'),
            platform: $this->string('Platform')->toString(),
        );
    }
}
