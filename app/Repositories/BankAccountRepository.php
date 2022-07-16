<?php


namespace App\Repositories;

use App\Models\BankAccount;
use App\Models\Client;

class BankAccountRepository
{

    private $model;

    public function __construct()
    {
        $this->model = new BankAccount();
    }

    public function index(Client $client)
    {
        return $this->model->whereHas('account', function ($query) use ($client) {
            return $query->where('client_id', $client->id);
        })->get();
    }


    public function getByUsername($username)
    {
        return $this->model->where('username', $username)
            ->first();
    }

    public function getById($id)
    {
        return $this->model->with('account', 'bank')->find($id);
    }


    public function store(array $req) // SHOULD INCLUDE CLIENT ID !
    {
        $account = $this->model->create($req);

        $account->account()->create([
            'type_id' => $req['type_id'],
            'client_id' => $req['client_id'],
            'currency_id' => $req['currency_id']
        ]);

        return $account;
    }

    public function update($bankAccount, array $req)
    {
        $bankAccount->update($req);
        return $bankAccount;
    }

    public function delete($bankAccount): ?bool
    {
        return $bankAccount->delete();
    }

}
