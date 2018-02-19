<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|regex:/^[!-~]+$/|min:3|max:15',
            'body' => 'required|string|regex:/^[a-zA-Z0-9\s]+/|min:3|max:30',
            'tags' => 'required|exists:tags,id'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'You forgot to input your name!',
            'name.regex' => 'Plese use half-width alphanumeric letters.',
            'body.required' => 'You cannot make a post without a comment.',
            'body.regex' => 'Please use alphanumeric letters',
            'tags.exists' => 'The tag you selected does not exist in the database'
        ];
    }

}
