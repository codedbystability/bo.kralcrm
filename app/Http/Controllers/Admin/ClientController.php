<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountType;
use App\Models\Client;
use App\Models\ClientAccountAgreement;
use App\Models\Financier;
use App\Repositories\AccountTypeRepository;
use App\Repositories\TransactionMethodRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ClientController extends Controller
{
    private $transactionMethodRepository, $accountTypeRepository;

    public function __construct()
    {
        $this->accountTypeRepository = new AccountTypeRepository();
        $this->transactionMethodRepository = new TransactionMethodRepository();
    }

    public function index()
    {
        $clients = Client::get();

        return view('admin.clients.index')->with([
            'clients' => $clients
        ]);
    }

    public function create()
    {
        return view('admin.clients.create');
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $username = $request->get('username');

        $check = Client::where('username', $username)->first();
        if ($check) {
            $this->setFlash('error', 'Kullanici Adi Kullanilamaz !');
            return Redirect::back();
        }

        $client = Client::create([
            'name' => $request->get('name'),
            'username' => $username,
            'password' => Hash::make($request->get('password')),
            'withdraw_percentage' => $request->get('withdraw_percentage'),
            'deposit_percentage' => $request->get('deposit_percentage'),
        ]);

        $clientAccountAgreementController = new ClientAccountController();
        $clientAccountAgreementController->store($client->id, null);


        $this->setFlash('success', 'Musteri Basariyla Olusturuldu !');
        return Redirect::route('admin.clients.index');

    }

    public function edit(Request $request, $id)
    {
        $client = Client::with('clientAccountAgreements.method', 'clientAccountAgreements.accountType')->find($id);

        $agreementAccountTypes = AccountType::get();
        if (!$client) {
            $this->setFlash('error', 'Musteri Bulunamadi !');
            return Redirect::back();
        }

        return view('admin.clients.edit')->with([
            'client' => $client,
            'accountTypes' => $agreementAccountTypes
        ]);
    }

    public function update(Request $request, $id): \Illuminate\Http\RedirectResponse
    {

        $client = Client::find($id);

        if (!$client) {
            $this->setFlash('error', 'Musteri Bulunamadi !');
            return Redirect::back();
        }

        $check = Client::where('username', $request->get('username'))
            ->where('id', '!=', $id)
            ->first();
        if ($check) {
            $this->setFlash('error', 'Kullanici Adi Kullanilamaz !');
            return Redirect::back();
        }

        $havaleAccountType = $this->accountTypeRepository->getByKey($request->get('havale-agreement'));
        $paparaAccountType = $this->accountTypeRepository->getByKey($request->get('papara-agreement'));

        $paparaMethod = $this->transactionMethodRepository->getByKey('papara');
        $havaleMethod = $this->transactionMethodRepository->getByKey('havale');

        $clientPaparaAgreement = ClientAccountAgreement::where([
            'client_id' => $client->id,
            'method_id' => $paparaMethod->id
        ])->first();

        $clientPaparaAgreement->update([
            'account_type_id' => $paparaAccountType->id
        ]);

        $clientHavaleAgreement = ClientAccountAgreement::where([
            'client_id' => $client->id,
            'method_id' => $havaleMethod->id
        ])->first();

        $clientHavaleAgreement->update([
            'account_type_id' => $havaleAccountType->id
        ]);

        $client->update([
            'username' => $request->get('username'),
            'name' => $request->get('name'),
            'password' => $request->get('password') ? Hash::make($request->get('password')) : $client->password,
            'withdraw_percentage' => $request->get('withdraw_percentage'),
            'deposit_percentage' => $request->get('deposit_percentage'),
        ]);


        $this->setFlash('success', 'Musteri bilgileri guncellendi');
        return Redirect::route('admin.clients.index');
    }

    public function destroy(Request $request, $id): \Illuminate\Http\RedirectResponse
    {
        $financier = Financier::find($id);
        if (!$financier || $financier->client_id !== Auth::id()) {
            $this->setFlash('error', 'Finansci Bulunamadi !');
            return Redirect::back();
        }
        $financier->delete();

        $this->setFlash('success', 'Finansci Basariyla Silindi !');

        return Redirect::route('financiers.index');

    }

    private function getAllPermissions()
    {
        $allPermissions = Permission::all();
        return [
            [
                'title' => 'Banka Hesaplari',
                'key' => 'bank_accounts',
                'values' => $allPermissions->where('top_group', 'bank_accounts')
            ],

            [
                'title' => 'Papara Hesaplari',
                'key' => 'papara_accounts',
                'values' => $allPermissions->where('top_group', 'papara_accounts')
            ],

            [
                'title' => 'Havale Bekleyen Yatirim',
                'key' => 'waiting_havale_transactions',
                'values' => $allPermissions->where('top_group', 'havale')
                    ->where('sub_group', 'deposit')
                    ->where('bottom_group', 'waiting')
            ],

            [
                'title' => 'Havale Onaylanan Yatirim',
                'key' => 'approved_havale_transactions',
                'values' => $allPermissions->where('top_group', 'havale')
                    ->where('sub_group', 'deposit')
                    ->where('bottom_group', 'approved')
            ],


            [
                'title' => 'Havale Tamamlanan Yatirim',
                'key' => 'completed_havale_transactions',
                'values' => $allPermissions->where('top_group', 'havale')
                    ->where('sub_group', 'deposit')
                    ->where('bottom_group', 'completed')
            ],

            [
                'title' => 'Havale Iptal Yatirim',
                'key' => 'cancelled_havale_transactions',
                'values' => $allPermissions->where('top_group', 'havale')
                    ->where('sub_group', 'deposit')
                    ->where('bottom_group', 'cancelled')
            ],


            [
                'title' => 'Papara Bekleyen Yatirim',
                'key' => 'waiting_papara_transactions',
                'values' => $allPermissions->where('top_group', 'papara')
                    ->where('sub_group', 'deposit')
                    ->where('bottom_group', 'waiting')
            ],

            [
                'title' => 'Papara Onaylanan Yatirim',
                'key' => 'approved_papara_transactions',
                'values' => $allPermissions->where('top_group', 'papara')
                    ->where('sub_group', 'deposit')
                    ->where('bottom_group', 'approved')
            ],


            [
                'title' => 'Papara Tamamlanan Yatirim',
                'key' => 'completed_papara_transactions',
                'values' => $allPermissions->where('top_group', 'papara')
                    ->where('sub_group', 'deposit')
                    ->where('bottom_group', 'completed')
            ],

            [
                'title' => 'Papara Iptal Yatirim',
                'key' => 'cancelled_papara_transactions',
                'values' => $allPermissions->where('top_group', 'papara')
                    ->where('sub_group', 'deposit')
                    ->where('bottom_group', 'cancelled')
            ],


            ///////////////////
            [
                'title' => 'Havale Bekleyen Cekim',
                'key' => 'waiting_havale_withdraws',
                'values' => $allPermissions->where('top_group', 'havale')
                    ->where('sub_group', 'withdraw')
                    ->where('bottom_group', 'waiting')
            ],

            [
                'title' => 'Havale Onaylanan Cekim',
                'key' => 'approved_havale_withdraws',
                'values' => $allPermissions->where('top_group', 'havale')
                    ->where('sub_group', 'withdraw')
                    ->where('bottom_group', 'approved')
            ],


            [
                'title' => 'Havale Tamamlanan Cekim',
                'key' => 'completed_havale_withdraws',
                'values' => $allPermissions->where('top_group', 'havale')
                    ->where('sub_group', 'withdraw')
                    ->where('bottom_group', 'completed')
            ],

            [
                'title' => 'Havale Iptal Cekim',
                'key' => 'cancelled_havale_withdraws',
                'values' => $allPermissions->where('top_group', 'havale')
                    ->where('sub_group', 'withdraw')
                    ->where('bottom_group', 'cancelled')
            ],


            [
                'title' => 'Papara Bekleyen Cekim',
                'key' => 'waiting_papara_withdraws',
                'values' => $allPermissions->where('top_group', 'papara')
                    ->where('sub_group', 'withdraw')
                    ->where('bottom_group', 'waiting')
            ],

            [
                'title' => 'Papara Onaylanan Cekim',
                'key' => 'approved_papara_withdraws',
                'values' => $allPermissions->where('top_group', 'papara')
                    ->where('sub_group', 'withdraw')
                    ->where('bottom_group', 'approved')
            ],


            [
                'title' => 'Papara Tamamlanan Cekim',
                'key' => 'completed_papara_withdraws',
                'values' => $allPermissions->where('top_group', 'papara')
                    ->where('sub_group', 'withdraw')
                    ->where('bottom_group', 'completed')
            ],

            [
                'title' => 'Papara Iptal Cekim',
                'key' => 'cancelled_papara_withdraws',
                'values' => $allPermissions->where('top_group', 'papara')
                    ->where('sub_group', 'withdraw')
                    ->where('bottom_group', 'cancelled')
            ],
        ];
    }

    private function setFlash($type, $message)
    {
        Session::flash('message', $message);
        Session::flash('type', $type);
    }

}
