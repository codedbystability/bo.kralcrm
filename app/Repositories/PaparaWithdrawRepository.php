<?php


namespace App\Repositories;

use App\Models\BankAccount;
use App\Models\Client;
use App\Models\Financier;
use App\Models\PaparaAccount;
use App\Models\PaparaWithdraw;

class PaparaWithdrawRepository
{

    private $model;

    public function __construct()
    {
        $this->model = new PaparaWithdraw();
    }


    public function getById($id)
    {
        return $this->model->with('transaction')->find($id);
    }

    public function store(array $req)
    {
        return $this->model->create($req);
    }


}
