<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\User;
use Auth;

class OldPasswordRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user=User::findOrfail($user);
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
        if(!\Hash::check(request('old_password'), $this->user->password) || empty(request('old_password'))){
            return false;
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
        return trans('admin.wrong-password');
    }
}
