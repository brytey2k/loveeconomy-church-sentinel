<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Dto\UssdInteractionRequestDto;
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
     * @return array<string, ValidationRule|array|string>
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

    public function toDto(): UssdInteractionRequestDto
    {
        return new UssdInteractionRequestDto(
            type: $this->post('Type'),
            message: $this->post('Message'),
            serviceCode: $this->post('ServiceCode'),
            operator: $this->post('Operator'),
            clientState: $this->post('ClientState'),
            mobile: $this->post('Mobile'),
            sessionId: $this->post('SessionId'),
            sequence: $this->integer('Sequence'),
            platform: $this->post('Platform')
        );
    }
}
