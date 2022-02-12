<?php


namespace App\Repositories;


use App\Models\TransactionType;
use Illuminate\Support\Facades\Cache;

class TransactionTypeRepository
{

    private $model;

    public function __construct()
    {
        $this->model = new TransactionType();
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
        return Cache::remember('transaction-type-' . $key, now()->addMinutes(5), function () use ($key) {
            return $this->model->where('key', $key)->first();
        });
    }

}
