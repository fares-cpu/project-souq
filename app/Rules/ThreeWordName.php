<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\Rule;

class ThreeWordName implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Check if the input consists of exactly three words separated by spaces
        $words = explode(' ', $value);

        if (count($words) !== 3) {
            return false;
        }
        
        // Check if each word is either English or Arabic (you may need to adjust this based on specific requirements)
        foreach ($words as $word) {
            if (!preg_match('/^[A-Za-z\s]+$|^[\x{0600}-\x{06FF}\s]+$/u', $word)) {
                return false;
            }        
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute must be a string consisting of exactly three words, either in English or Arabic.';
    }
}
