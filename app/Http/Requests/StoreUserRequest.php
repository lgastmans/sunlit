<?php

namespace App\Http\Requests;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Auth\Access\AuthorizationException;



class StoreUserRequest extends FormRequest
{
    protected function failedAuthorization()
    {
        throw new AuthorizationException(trans('app.unauthorized'));
    }


    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('edit users');
    }

    protected function prepareForValidation()
    {

        if ($this->get('role-select')){
            $roleInfo = Role::findById($this->get('role-select'));
            $role = $roleInfo->name;
        }
        else{
            $role = $this->get('role');
        }
        $this->merge(['role'=>$role]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|max:255|unique:users,name,'.$this->id,
            'email' => 'required|email:rfc,dns|max:255|unique:users,email,'.$this->id,
            'role' => 'required',
        ];
    }
}
