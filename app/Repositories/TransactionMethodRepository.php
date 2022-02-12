<?php


namespace App\Repositories;

use App\Models\Client;
use App\Models\Transaction;
use App\Models\TransactionMethod;
use App\Models\TransactionStatus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TransactionMethodRepository
{

    private $model;

    public function __construct()
    {
        $this->model = new TransactionMethod();
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
        Log::info($key);
        return Cache::remember('transaction-method-' . $key, now()->addMinutes(5), function () use ($key) {
            return $this->model->where('key', $key)->first();
        });
    }

}
