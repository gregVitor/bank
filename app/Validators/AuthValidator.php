<?php

namespace App\Validators;

class AuthValidator extends Validator
{
    public function validateRegisterUser(array $data)
    {
        $rules = [
            'name'             => 'required|string',
            'cpf'              => 'required|unique:user',
            'password'         => 'required|min:8|string',
            'birthday'         => 'required|string',
            'password_confirm' => 'required|same:password'
        ];

        return $this->validate($data, $rules);
    }

    public function validateLogin(array $data)
    {
        $rules = [
            'cpf'      => 'required|string',
            'password' => 'required|string'
        ];

        return $this->validate($data, $rules);
    }
}
