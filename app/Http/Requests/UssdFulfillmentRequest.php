<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UssdFulfillmentRequest extends FormRequest
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
            'SessionId' => ['required', 'string'],
            'OrderId' => ['required', 'string'],
            'OrderInfo' => ['required', 'array'],
            'OrderInfo.CustomerMobileNumber' => ['required', 'string'],
            'OrderInfo.CustomerName' => ['required', 'string'],
            'OrderInfo.OrderDate' => ['required', 'date'],
            'OrderInfo.Currency' => ['required', 'string'],
            'OrderInfo.BranchName' => ['required', 'string'],
            'OrderInfo.Payment' => ['required', 'array'],
            'OrderInfo.Payment.PaymentType' => ['required', 'string'],
            'OrderInfo.Payment.AmountPaid' => ['required', 'numeric'],
            'OrderInfo.Payment.AmountAfterCharges' => ['required', 'numeric'],
            'OrderInfo.Payment.PaymentDate' => ['required', 'date'],
            'OrderInfo.Payment.PaymentDescription' => ['required', 'string'],
            'OrderInfo.Payment.IsSuccessful' => ['required', 'boolean'],
        ];
    }
}
