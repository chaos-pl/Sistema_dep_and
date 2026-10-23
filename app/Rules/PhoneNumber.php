<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PhoneNumber implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! preg_match('/\A[0-9]{10}\z/', $value)) {
            $fail('El teléfono debe contener exactamente 10 dígitos, sin letras, espacios ni guiones.');
        }
    }
}
