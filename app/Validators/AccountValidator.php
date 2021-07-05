<?php

namespace App\Validators;

class AccountValidator extends Validator
{

    /**
     * Function Validate create account deposit
     *
     * @param array $data
     *
     * @return bool
     */
    public function createAccountDeposit(array $data)
    {
        $rules = [
            'amount' => 'required|integer|not_in:0|min:0'
        ];

        if (isset($data['amount']) && isset($data['units'])) {
            abort(400, "Requisição inválida");
        }

        return $this->validate($data, $rules);
    }

     /**
     * Function Validate create account draft
     *
     * @param array $data
     *
     * @return bool
     */
    public function createAccountDraft(array $data)
    {
        $rules = [
            'amount' => 'required|integer|not_in:0|min:20'
        ];

        if (isset($data['amount']) && isset($data['units'])) {
            abort(400, "Requisição inválida");
        }

        return $this->validate($data, $rules);
    }
}
