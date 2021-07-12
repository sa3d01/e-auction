<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\Api\ApiMasterRequest;
use App\Utils\PreparePhone;
use Illuminate\Http\Exceptions\HttpResponseException;

class PhoneUpdateRequest extends ApiMasterRequest
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
        if ($this->has('new_phone')) {
            $phone = new PreparePhone($this->new_phone);
            if (!$phone->isValid()) {
                throw new HttpResponseException(response()->json([
                    'status' =>400,
                    'message' => $phone->errorMsg()
                ],200));
            }
            $this->merge(['new_phone' => $phone->getNormalized()]);
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
            'new_phone' => 'required|string|max:90|unique:users,phone',
        ];
    }
    public function messages()
    {
        return [
            'new_phone.unique' => 'هذا الهاتف مسجل من قبل',
        ];
    }
}
