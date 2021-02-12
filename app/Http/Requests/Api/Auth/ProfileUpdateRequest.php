<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\Api\ApiMasterRequest;
use App\Utils\PreparePhone;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProfileUpdateRequest extends ApiMasterRequest
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
    protected function prepareForValidation()
    {
        if ($this->has('phone')) {
            $phone = new PreparePhone($this->phone);
            if (!$phone->isValid()) {
                throw new HttpResponseException(response()->json([
                    'status' =>400,
                    'message' => $phone->errorMsg()
                ], 400));
            }
            $this->merge(['phone' => $phone->getNormalized()]);
        }
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:110',
            'email' => 'email|max:90|unique:users,email,' . \request()->user()->id,
            'phone' => 'required|string|max:90|unique:users,phone,' . \request()->user()->id,
            'device.id' => 'required',
            'device.type' => 'required|in:android,ios',
        ];
    }
    public function messages()
    {
        return [
            'phone.unique' => 'هذا الهاتف مسجل من قبل',
            'email.unique' => 'هذا البريد مسجل من قبل',
        ];
    }
}
