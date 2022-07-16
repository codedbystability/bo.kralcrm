<?php

namespace App\Http\Controllers\Financier;


use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Bank;
use App\Models\BankAccount;
use App\Models\Client;
use App\Models\ClientFinancier;
use App\Models\Currency;
use App\Repositories\BankAccountRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class BankAccountController extends Controller
{

    private $bankAccountRepository;

    public function __construct()
    {
        $this->bankAccountRepository = new BankAccountRepository();
    }

    public function getBankAccountsToList($clientIds, $clientID = null, $currencyID = null)
    {
        return Account::whereIn('client_id', $clientIds)
            ->whereHasMorph('accountable', BankAccount::class, function ($query) use ($clientID, $currencyID) {
                return $query
                    ->when($clientID, function ($query) use ($clientID) {
                        return $query->where('client_id', $clientID);
                    })
                    ->when($currencyID, function ($query) use ($currencyID) {
                        return $query->where('currency_id', $currencyID);
                    })

                    ->with(['accountable' => function ($query) {
                        return $query
                            ->with(['bank' => function ($q) {
                                return $q->select('id', 'name', 'image');
                            }])
                            ->with(['currency' => function ($q) {
                                return $q->select('id', 'name', 'local_name', 'symbol');
                            }])
                            ->select('id', 'currency_id', 'bank_id', 'accno', 'iban', 'owner', 'branch', 'min_deposit', 'max_deposit', 'min_withdraw', 'max_withdraw');
                    }]);
            })->with(['type' => function ($q) {
                return $q->select('id', 'name', 'key');
            }])
            ->with('client')
            ->paginate(20);
    }

    public function index()
    {
        $clientIds = ClientFinancier::where('financier_id', Auth::id())->pluck('client_id')->toArray();
        $clients = Client::whereIn('id', $clientIds)->get();
        $currencies = Currency::where('is_active', true)->get();

        $accounts = $this->getBankAccountsToList($clientIds, null, null);

        return view('financier.bank-accounts.index')->with([
            'data' => $accounts,
            'clients' => $clients,
            'currencies' => $currencies
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

        $accounts = $this->getBankAccountsToList($clientIds, $clientID, $currencyID);


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
        $clientIds = ClientFinancier::where('financier_id', Auth::id())->pluck('client_id')->toArray();
        $banks = Bank::get();
        $clients = Client::whereIn('id', $clientIds)->get();
        $currencies = Currency::get();


        return view('financier.bank-accounts.create')->with([
            'banks' => $banks,
            'clients' => $clients,
            'currencies' => $currencies,
        ]);
    }

    public function store(Request $request)
    {
        $this->bankAccountRepository->store(array_merge($request->all(), ['financier_id' => Auth::id()]));

        Session::flash('message', 'Banka hesabi sisteme eklendi !');
        Session::flash('type', 'success');

        return Redirect::route('financier.bank-accounts.index');

    }

    public function edit(Request $request, $id)
    {
        $bankAccount = $this->bankAccountRepository->getById($id);

        if (!$bankAccount) {
            Session::flash('message', 'Hesap bulunamadi !');
            Session::flash('type', 'error');
            return Redirect::back();
        }
        $banks = Bank::get();

        return view('financier.bank-accounts.edit')->with([
            'bankAccount' => $bankAccount,
            'banks' => $banks

        ]);
    }

    public function update(Request $request, $id)
    {
        $bankAccount = $this->bankAccountRepository->getById($id);

        if (!$bankAccount) {
            Session::flash('message', 'Hesap bulunamadi !');
            Session::flash('type', 'error');
            return Redirect::back();
        }

        $this->bankAccountRepository->update($bankAccount, $request->all());


        Session::flash('message', 'Banka hesabi guncellendi !');
        Session::flash('type', 'success');

        return Redirect::route('financier.bank-accounts.index');

    }

    public function destroy(Request $request, $id)
    {
        $bankAccount = $this->bankAccountRepository->getById($id);

        if (!$bankAccount) {
            Session::flash('message', 'Hesap bulunamadi !');
            Session::flash('type', 'error');
            return Redirect::back();
        }

        $this->bankAccountRepository->delete($bankAccount);

        Session::flash('message', 'Banka hesabi silindi !');
        Session::flash('type', 'success');

        return Redirect::route('financier.bank-accounts.index');

    }

    public function activate($id)
    {
        $acc = Account::findOrFail($id);
        $acc->update([
            'is_active' => !$acc->is_active
        ]);
        return Redirect::route('financier.bank-accounts.index');
    }
}
