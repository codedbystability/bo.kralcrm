<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientAccountAgreement;
use App\Repositories\TransactionMethodRepository;
use Illuminate\Http\Request;

class ClientAccountController extends Controller
{
    private $transactionMethodRepository;

    public function __construct()
    {
        $this->transactionMethodRepository = new TransactionMethodRepository();
    }

    public function store($clientID, $accountTypeID = null)
    {
        ClientAccountAgreement::create([
            'client_id' => $clientID,
            'method_id' => $this->transactionMethodRepository->getByKey('papara')->id,
            'account_type_id' => $accountTypeID ? $accountTypeID : 3
        ]);

        return ClientAccountAgreement::create([
            'client_id' => $clientID,
            'method_id' => $this->transactionMethodRepository->getByKey('havale')->id,
            'account_type_id' => $accountTypeID ? $accountTypeID : 3
        ]);
    }
}
