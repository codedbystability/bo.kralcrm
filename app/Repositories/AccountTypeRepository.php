<?php


namespace App\Repositories;

use App\Models\AccountType;
use App\Models\ClientAccountAgreement;

class AccountTypeRepository
{

    private $model;

    public function __construct()
    {
        $this->model = new AccountType();
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
