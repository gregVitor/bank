<?php

namespace App\Repositories;

use App\Models\Account;

class AccountRepository
{

    /**
     * @var Account
     */
    private $account;

    /**
     * Class constructor method.
     *
     * @param Account $Account
     */
    public function __construct(
        Account $account
    ) {
        $this->account = $account;
    }

    public function createAccount(
        $user,
        $type
    ) {
        return $this->createAccountDeposit($user->id, 0, $type);
    }

    /**
     * Function get balance user
     *
     * @param object $data
     * @return float
     */
    public function getBalance(
        int    $userId,
        string $type = 'c'
    ) {
        $balance = $this->account->where('user_id', $userId)->where('type', $type)->sum('amount');

        return round($balance, 2);
    }

    /**
     * Function account deposit
     *
     * @param integer $userId
     * @param float $amount
     * @param string $type
     *
     * @return object
     */
    public function createAccountDeposit(
        int    $userId,
        float  $amount,
        string $type = 'c',
        string $operation = 'deposit'

    ) {
        $deposit = $this->createTransactionAccount($userId, $type, $amount, $operation);

        $balance = $this->getBalance($userId, $type);

        $data = (object) [
            'id'         => $deposit->id,
            'deposit'    => $deposit->amount,
            'balance'    => $balance,
            'created_at' => date('Y-m-d H:i:s', strtotime($deposit->created_at))
        ];

        return $data;
    }

    /**
     * Function get Extract to filter
     *
     * @param integer $userId
     * @param object $data
     * @param object $paginator
     *
     * @return void
     */
    public function getExtract(
        int    $userId,
        object $data = null,
        object $paginator = null
    ) {

        $accounts = $this->account->where('user_id', $userId);

        if (!empty($paginator->per_page)) {
            $accounts = $accounts->paginate($paginator->per_page, ['*'], 'page', $paginator->page);
        } else {
            $accounts = $accounts->get();
        }

        $dataReturn = [];
        foreach ($accounts as $account) {
            $data = (object) [
                'id'         => hashEncodeId($account->id),
                'amount'     => $account->amount,
                'type'       => $account->type,
                'operation'  => $account->operation,
                'created_at' => date('Y-m-d H:i', strtotime($account->created_at))
            ];

            $dataReturn[] = $data;
        }

        $arrayReturn = ['data' => $dataReturn];

        if (!empty($paginator->per_page)) {
            $paginate = [
                'total'             => $accounts->total(),
                'current_page'      => $accounts->currentPage(),
                'last_page'         => $accounts->lastPage(),
                'per_page'          => $accounts->perPage(),
                'next_page_url'     => $accounts->nextPageUrl(),
                'previous_page_url' => $accounts->previousPageUrl()
            ];
            $arrayReturn['paginate'] = $paginate;
        }

        return $arrayReturn;
    }

    /**
     * Function get  accounts
     *
     * @param string $date
     *
     * @return void
     */
    public function getAccounts(
        string $date = null
    ) {
        $accounts = $this->account;

        if (!empty($date)) {
            $accounts = $accounts->whereDate('created_at', '=', date('Y-m-d', strtotime($date)));
        }

        $accounts = $accounts->get();

        return $accounts;
    }

    public function draftAccount(
        int    $userId,
        float  $amount,
        string $type,
        string $operation = 'draft'
    ) {
        $draft = $this->createDraftAccount($userId, $type, $amount, $operation);

        return $draft;
    }

    private function createDraftAccount(
        int    $userId,
        string $type,
        float  $amount,
        string $operation
    ) {

        $account = $this->account->create([
            "user_id"   => $userId,
            "type"      => $type,
            "amount"    => $amount,
            "operation" => $operation
        ]);

        return $account;
    }

    private function createTransactionAccount(
        int    $userId,
        string $type,
        float  $amount,
        string $operation
    ) {

        $account = $this->account->create([
            "user_id"   => $userId,
            "type"      => $type,
            "amount"    => $amount,
            "operation" => $operation
        ]);

        return $account;
    }
}
