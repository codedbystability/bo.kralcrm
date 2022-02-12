<?php


namespace App\Repositories;

use App\Models\Client;
use App\Models\Transaction;
use App\Models\TransactionStatus;
use Illuminate\Support\Facades\Cache;

class TransactionStatusRepository
{

    private $model;

    public function __construct()
    {
        $this->model = new TransactionStatus();
    }

    public function getAll()
    {
        return $this->model->get();
    }

    public function getById($id)
    {
        return $this->model->find($id);
    }

    public function getByKey($key)
    {
        return $this->model->where('key', $key)->first();
    }

}
