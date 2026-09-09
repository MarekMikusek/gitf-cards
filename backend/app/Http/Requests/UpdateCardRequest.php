<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCardRequest extends FormRequest
{
    public function rules(): array
    {
        $cardId = $this->route('card')?->id ?? $this->route('card');

        return [
            'card_number' => [
                'required',
                'string',
                'size:20',
                'regex:/^[0-9]{20}$/',
                Rule::unique('cards', 'card_number')->ignore($cardId),
            ],
            'pin' => ['required', 'string', 'size:4', 'regex:/^[0-9]{4}$/'],
            'activation_date' => ['required', 'date'],
            'expiration_date' => ['required', 'date', 'after_or_equal:activation_date'],
            'balance' => ['required', 'numeric', 'min:0'],
        ];
    }
}
