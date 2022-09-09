<?php

namespace Hanoivip\Ddd2\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'username' => ['required', 'string', 'min:6', 'max:32'],
            'password' => ['required', 'string', 'min:6'],
        ];
    }
}
