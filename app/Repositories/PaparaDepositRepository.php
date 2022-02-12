<?php


namespace App\Repositories;

use App\Models\PaparaDeposit;

class PaparaDepositRepository
{

    private $model;

    public function __construct()
    {
        $this->model = new PaparaDeposit();
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
