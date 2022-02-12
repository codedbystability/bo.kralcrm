<?php


namespace App\Repositories;

use App\Models\Financier;
use App\Models\FinancierAction;
use App\Models\Transaction;
use App\Models\TransactionAction;

class TransactionActionRepository
{

    private $model, $financierAction;

    public function __construct()
    {
        $this->model = new TransactionAction();
        $this->financierAction = new FinancierAction();
    }


    public function getByKey($transactionActionKey)
    {
        return $this->financierAction->where('key', $transactionActionKey)
            ->first();
    }

    public function action($financier, $transaction, $transactionActionKey)
    {
        $action = $this->getByKey($transactionActionKey);

        return $this->model->create([
            'transaction_id' => $transaction->id,
            'action_id' => $action->id,
            'financier_id' => $financier->id
        ]);
    }


}
