<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class OncePerMinuteOnly implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $lastReply = auth()->user()->lastReply;
        if (! $lastReply) return true;

        return ! auth()->user()->lastReply->wasJustPublished();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'You are posting too frequently. Wait one minute before posting again.';
    }
}
