<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Repositories\AccountRepository;
use App\Services\AccountService;
use App\Validators\AccountValidator;
use Illuminate\Http\Request;

class AccountController extends Controller
{

    /**
     * @var AccountRepository
     */
    private $accountRepository;

    /**
     * @var AccountService
     */
    private $accountService;

    /**
     * @var AccountValidator
     */
    private $accountValidator;

    /**
     * Class constructor method.
     *
     * @param AccountRepository $accountRepository
     * @param AccountService $accountService
     * @param AccountValidator $accountValidator
     */
    public function __construct(
        AccountRepository $accountRepository,
        AccountService    $accountService,
        AccountValidator  $accountValidator
    ) {
        $this->accountRepository = $accountRepository;
        $this->accountService    = $accountService;
        $this->accountValidator  = $accountValidator;
    }

    public function createAccount(Request $request)
    {
        try {

            $account = $this->accountRepository->createAccount($request->user, $request->type);

            return apiResponse("Conta criada com sucesso.", 200, $account);
        } catch (\Exception $e) {
            throw ($e);
        }
    }

    /**
     * Function create deposit
     *
     * @param Request $request
     *
     * @return void
     */
    public function createAccountDeposit(Request $request)
    {
        try {

            $this->accountValidator->createAccountDeposit($request->all());

            $balance = $this->accountService->createAccountDeposit($request->user, $request->amount, $request->type, 'deposit');

            return apiResponse("Valor depositado em conta.", 200, $balance);
        } catch (\Exception $e) {
            throw ($e);
        }
    }

    /**
     * Function get user account balance
     *
     * @param Request $request
     *
     * @return void
     */
    public function getBalance(Request $request)
    {
        try {
            $balance = $this->accountRepository->getBalance($request->user->id, $request->type);

            $data = [
                'balance' => $balance
            ];

            return apiResponse("Ok.", 200, $data);
        } catch (\Exception $e) {
            throw ($e);
        }
    }

    /**
     * Function get extract
     *
     * @param Request $request
     *
     * @return void
     */
    public function getExtract(Request $request)
    {
        try {
            $data      = (object) [];
            $paginator = (object) [];

            if (isset($request->initial_date)) {
                $data->initial_date = $request->initial_date;
            }
            if (isset($request->final_date)) {
                $data->final_date = $request->final_date;
            }
            if (isset($request->per_page)) {
                $paginator->per_page = $request->per_page;
            }
            if (isset($request->page)) {
                $paginator->page = $request->page;
            }

            $extract = $this->accountRepository->getExtract($request->user->id, $data, $paginator);

            return apiResponse("Ok.", 200, $extract);
        } catch (\Exception $e) {
            throw ($e);
        }
    }

    public function draft(Request $request)
    {
        try {

            $this->accountValidator->createAccountDraft($request->all());

            $balance = $this->accountService->createAccountDraft($request->user, $request->amount, $request->type, 'draft');

            return apiResponse("Valor sacado.", 200, $balance);
        } catch (\Exception $e) {
            throw ($e);
        }
    }
}
