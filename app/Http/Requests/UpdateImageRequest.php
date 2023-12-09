<?php

namespace App\Http\Requests;

use App\Models\Log;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateImageRequest extends FormRequest
{
    /**
     * Indicates if the validator should stop on the first rule failure.
     *
     * @var bool
     */
    protected $stopOnFirstFailure = true;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'profileImage' => 'required|image|mimes:jpg,jpeg,png|file|max:8192'
        ];
    }
    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'profileImage.required' => 'Profile image not provided!',
            'profileImage.image' => 'Provided file is not an image!',
            'profileImage.mimes' => 'Allowed file types are jpg, jpeg and png!',
            'profileImage.max' => 'Maximum allowed file size is 8MB!',
        ];
    }


    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    protected function failedValidation($validator)
    {
        // Log additional information about the failed validation
        Log::errorLog('Error: '.$validator->errors()->first('profileImage'), Auth::user()->user_id);

        // this keeps the original functionality like throwing an error and redirection from FormRequest parent class
        parent::failedValidation($validator);
    }
}
