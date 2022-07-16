<?php

namespace App\Http\Controllers\Financier;


use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Bank;
use App\Models\BankAccount;
use App\Models\Client;
use App\Models\ClientFinancier;
use App\Models\Currency;
use App\Models\PaparaAccount;
use App\Repositories\PaparaAccountRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class PaparaAccountController extends Controller
{

    private $paparaAccountRepository;

    public function __construct()
    {
        $this->paparaAccountRepository = new PaparaAccountRepository();
    }

    public function getPaparaAccountsToList($clientID = false, $currencyID = false)
    {
        return Account::where('id', '!=', 0)
            ->when($clientID, function ($query) use ($clientID) {
                return $query->where('client_id', $clientID);
            })
            ->when($currencyID, function ($query) use ($currencyID) {
                return $query->where('currency_id', $currencyID);
            })
            ->whereHasMorph('accountable', PaparaAccount::class, function ($query) {
                return $query->with(['accountable' => function ($query) {
                    return $query->select('id', 'currency_id', 'accno', 'owner', 'min_deposit', 'max_deposit', 'min_withdraw', 'max_withdraw');
                }]);
            })
            ->has('type')
            ->with(['type' => function ($q) {
                return $q->select('id', 'name', 'key');
            }])
            ->with(['currency' => function ($q) {
                return $q->select('id', 'name', 'local_name', 'symbol');
            }])
            ->paginate(10);

    }


    public function index()
    {
        $clientIds = ClientFinancier::where('financier_id', Auth::id())->pluck('client_id')->toArray();

        $accounts = $this->getPaparaAccountsToList();
        $currencies = Currency::where('is_active', true)->get();
        $clients = Client::whereIn('id', $clientIds)->get();

        return view('financier.papara-accounts.index')->with([
            'data' => $accounts,
            'currencies' => $currencies,
            'clients' => $clients,
            'currencyID' => null,
            'clientID' => null

        ]);
    }

    public function filter(Request $request)
    {
        $clientID = null;
        $currencyID = null;
        if ($request->exists('client_id') && $request->get('client_id') != '') {
            $clientID = $request->get('client_id');
        }
        if ($request->exists('currency_id') && $request->get('currency_id') != '') {
            $currencyID = $request->get('currency_id');
        }


        $clientIds = ClientFinancier::where('financier_id', Auth::id())->pluck('client_id')->toArray();
        $clients = Client::whereIn('id', $clientIds)->get();
        $currencies = Currency::where('is_active', true)->get();

        dd($clientID, $currencyID);
        $accounts = $this->getPaparaAccountsToList($clientID, $currencyID);


        return view('financier.bank-accounts.index')->with([
            'data' => $accounts,
            'clients' => $clients,
            'currencies' => $currencies,
            'currencyID' => $currencyID,
            'clientID' => $clientID
        ]);
    }


    public function create()
    {
        $clients = Client::all();
        $currencies = Currency::get();

        return view('financier.papara-accounts.create')->with([
            'clients' => $clients,
            'currencies' => $currencies,

        ]);
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $this->paparaAccountRepository->store(array_merge($request->all()));

        Session::flash('message', 'Papara hesabi sisteme eklendi !');
        Session::flash('type', 'success');

        return Redirect::route('financier.papara-accounts.index');

    }

    public function edit(Request $request, $id)
    {
        $bankAccount = $this->paparaAccountRepository->getById($id);

        if (!$bankAccount) {
            Session::flash('message', 'Hesap bulunamadi !');
            Session::flash('type', 'error');
            return Redirect::back();
        }
        $banks = Bank::get();

        return view('financier.papara-accounts.edit')->with([
            'bankAccount' => $bankAccount,
            'banks' => $banks
        ]);
    }

    public function update(Request $request, $id)
    {
        $bankAccount = $this->paparaAccountRepository->getById($id);

        if (!$bankAccount) {
            Session::flash('message', 'Hesap bulunamadi !');
            Session::flash('type', 'error');
            return Redirect::back();
        }

        $this->paparaAccountRepository->update($bankAccount, $request->all());


        Session::flash('message', 'Papara hesabi guncellendi !');
        Session::flash('type', 'success');

        return Redirect::route('financier.papara-accounts.index');

    }

    public function destroy(Request $request, $id)
    {
        $bankAccount = $this->paparaAccountRepository->getById($id);

        if (!$bankAccount) {
            Session::flash('message', 'Hesap bulunamadi !');
            Session::flash('type', 'error');
            return Redirect::back();
        }

        $this->paparaAccountRepository->delete($bankAccount);

        Session::flash('message', 'Papara hesabi silindi !');
        Session::flash('type', 'success');

        return Redirect::route('financier.papara-accounts.index');

    }
}
