<?php

namespace App\Rules;

use Closure;
use Hash;
use Illuminate\Contracts\Validation\ValidationRule;

class CheckPasswordRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!Hash::check($value, auth()->user()->password)) {
            $fail("The :attribute is not valid.")->translate();
        }
    }
}