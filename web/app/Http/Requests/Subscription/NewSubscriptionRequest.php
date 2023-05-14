<?php

namespace App\Http\Requests\Subscription;

use Illuminate\Foundation\Http\FormRequest;

class NewSubscriptionRequest extends FormRequest
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
            'subscriptionPlanId' => 'required|exists:subscription_plans,id'
        ];
    }

    /**
     *--------------------------------------------------------------------------
     * Validation error messages
     *--------------------------------------------------------------------------
     *
     * @param 
     * @return array
     */

    public function messages()
    {
        return [
            'subscriptionPlanId.required' => ':attribute is required.',
            'subscriptionPlanId.exists' => ':attribute doesn\'t exist.'
        ];
    }

    /**
     *--------------------------------------------------------------------------
     * Atributes
     *--------------------------------------------------------------------------
     *
     * @param 
     * @return array
     */

    public function attributes()
    {
        return [
            'subscriptionPlanId' => 'Subscription plan'
        ];
    }
}
