<?php

namespace App\Services;

use App\Repositories\AccountRepository;

class AccountService
{
    /**
     * @var AccountRepository
     */
    private $accountRepository;

    /**
     * Class constructor method.
     *
     */
    public function __construct(
        AccountRepository $accountRepository
    ) {
        $this->accountRepository = $accountRepository;
    }

    /**
     * Function create deposit
     *
     * @param object $user
     * @param float $amount
     *
     * @return void
     */
    public function createAccountDeposit(
        object $user,
        float  $amount,
        string $type,
        string $operation
    ) {
        $balance = $this->accountRepository->createAccountDeposit($user->id, $amount, $type, $operation);
        $balance->id = hashEncodeId($balance->id);


        return ($balance);
    }
}
