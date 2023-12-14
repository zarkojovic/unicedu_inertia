<?php

namespace App\Http\Requests;

use App\Models\Log;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class OutboundValidationRequest extends FormRequest
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
            'auth.application_token' => 'required|in:' . env("BITRIX_APPLICATION_TOKEN"),
            'event' => 'required|in:ONCRMDEALUPDATE,ONCRMCONTACTUPDATE', // Add more events as needed
            'auth.domain' => 'required|in:' . env("OUTBOUND_DOMAIN"),
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
        Log::errorLog('Error: '.$validator->errors());
        abort(response()->json(['error' => 'Failed validating request.'], 422));
    }
}
