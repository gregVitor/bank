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
        $balance     = $this->accountRepository->createAccountDeposit($user->id, $amount, $type, $operation);
        $balance->id = hashEncodeId($balance->id);

        return ($balance);
    }

    /**
     * Function create draft
     *
     * @param object $user
     * @param float $amount
     *
     * @return void
     */
    public function createAccountDraft(
        object $user,
        float  $amount,
        string $type
    ) {
        $balance = $this->accountRepository->getBalance($user->id, $type);

        if (!empty($amount)) {
            if ($amount > $balance) {
                abort(400, "Saldo insuficente, para está operação.");
            }
        }


        $data = $this->gradeCount($amount);
        $this->createDraft($user->id, $amount, $type);

        return ($data);
    }

    private function createDraft(
        int    $userId,
        float  $amount,
        string $type

    ) {
        $this->accountRepository->draftAccount($userId, $amount * -1, $type);

        return true;
    }

    private function returnAmount(
        $amount,
        $moneyBills
    ) {
        $grades = $amount / $moneyBills;
        $rest   = $amount % $moneyBills;

        return (object) [
            "grades" => floor($grades),
            "rest"   => $rest
        ];
    }

    private function gradeCount($amount)
    {
        if (0 != $amount) {
            $hundredMoneyBills = $this->returnAmount($amount, 100);
        }

        if (0 != $amount) {
            $fiftyMoneyBills = $this->returnAmount($hundredMoneyBills->rest, 50);
        }

        if (0 != $amount) {
            $twentyMoneyBills = $this->returnAmount($fiftyMoneyBills->rest, 20);
        }

        if ($twentyMoneyBills->rest > 0) {
            $hundredMoneyBills->grades = 0;
            $fiftyMoneyBills->grades   = 0;
            $twentyMoneyBills->grades  = 0;

            if (0 != $amount) {
                $fiftyMoneyBills = $this->returnAmount($amount, 50);
            }

            if (0 != $amount) {
                $twentyMoneyBills = $this->returnAmount($fiftyMoneyBills->rest, 20);
            }

            if ($twentyMoneyBills->rest > 0) {
                $hundredMoneyBills->grades = 0;
                $fiftyMoneyBills->grades   = 0;
                $twentyMoneyBills->grades  = 0;

                if (0 != $amount) {
                    $twentyMoneyBills = $this->returnAmount($amount, 20);
                }
            }

            if ($twentyMoneyBills->rest > 0) {
                abort(400, "Saldo insuficente, para está operação.");
            }
        }

        return (object) [
            "amount"        => $amount,
            "gradesHundred" => $hundredMoneyBills->grades,
            "gradesFifty"   => $fiftyMoneyBills->grades,
            "gradesTwenty"  => $twentyMoneyBills->grades
        ];
    }
}
