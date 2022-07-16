<?php


namespace App\Repositories;

use App\Models\BankAccount;
use App\Models\Client;
use App\Models\Financier;
use App\Models\PaparaAccount;

class PaparaAccountRepository
{

    private $model;

    public function __construct()
    {
        $this->model = new PaparaAccount();
    }

    public function index(Client $client)
    {
        return $this->model->whereHas('account', function ($query) use ($client) {
            return $query->where('client_id', $client->id);
        })->get();
    }

    public function setClient(Client $client)
    {
        $this->client = $client;
    }


    public function getByUsername($username)
    {
        return $this->model->where('username', $username)
            ->first();
    }

    public function getById($id)
    {
        return $this->model->with('account')->find($id);
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

    public function update($paparaAccount, array $req)
    {
        $paparaAccount->update($req);
        return $paparaAccount;
    }

    public function delete($bankAccount): ?bool
    {
        return $bankAccount->delete();
    }

}
