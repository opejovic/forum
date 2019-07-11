<?php

namespace App\Http\Requests;

use App\Exceptions\ThrottleException;
use App\Rules\SpamFree;
use Illuminate\Foundation\Http\FormRequest;

class CreatePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return \Gate::allows('create', new \App\Models\Reply);
    }

    /**
     * Handle a failed authorization attempt.
     *
     * @return void
     * @throws \App\Exceptions\ThrottleException
     */
    protected function failedAuthorization()
    {
        throw new ThrottleException();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'body' => [
                'required',
                'min:2',
                new Spamfree,
            ],
        ];
    }
}
