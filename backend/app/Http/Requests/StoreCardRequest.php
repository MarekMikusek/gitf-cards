<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCardRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'card_number' => ['required', 'string', 'size:20', 'regex:/^[0-9]{20}$/', 'unique:cards,card_number'],
            'pin' => ['required', 'string', 'size:4', 'regex:/^[0-9]{4}$/'],
            'activation_date' => ['required', 'date'],
            'expiration_date' => ['required', 'date', 'after_or_equal:activation_date'],
            'balance' => ['required', 'numeric', 'min:0'],
        ];
    }
}
