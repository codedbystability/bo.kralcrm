<?php

namespace App\Http\Controllers\Client;


use App\Http\Controllers\Controller;
use App\Models\AccountType;
use App\Models\Client;
use App\Models\ClientAccountAgreement;
use App\Models\Financier;
use App\Models\Transaction;
use App\Models\TransactionStatus;
use App\Models\User;
use App\Repositories\AccountTypeRepository;
use App\Repositories\TransactionMethodRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class ProfileController extends Controller
{

    private $transactionMethodRepository, $accountTypeRepository;

    public function __construct()
    {
        $this->accountTypeRepository = new AccountTypeRepository();
        $this->transactionMethodRepository = new TransactionMethodRepository();
    }


    public function profile()
    {
        $client = Client::with('clientAccountAgreements.method', 'clientAccountAgreements.accountType')->find(Auth::id());
        $agreementAccountTypes = AccountType::get();
        return view('client.profile.index')->with([
            'client' => $client,
            'accountTypes' => $agreementAccountTypes
        ]);
    }

    public function profileUpdate(Request $request)
    {
        $checkUsername = Client::where('username', $request->get('username'))
            ->where('id', '!=', Auth::id())
            ->first();

        if ($checkUsername) {
            $this->setFlash('error', 'Kullanici Adi Kullanilamaz');
            return Redirect::back();
        }

        $havaleAccountType = $this->accountTypeRepository->getByKey($request->get('havale-agreement'));
        $paparaAccountType = $this->accountTypeRepository->getByKey($request->get('papara-agreement'));

        $paparaMethod = $this->transactionMethodRepository->getByKey('papara');
        $havaleMethod = $this->transactionMethodRepository->getByKey('havale');

        $clientPaparaAgreement = ClientAccountAgreement::where([
            'client_id' => Auth::id(),
            'method_id' => $paparaMethod->id
        ])->first();

        $clientPaparaAgreement->update([
            'account_type_id' => $paparaAccountType->id
        ]);

        $clientHavaleAgreement = ClientAccountAgreement::where([
            'client_id' => Auth::id(),
            'method_id' => $havaleMethod->id
        ])->first();

        $clientHavaleAgreement->update([
            'account_type_id' => $havaleAccountType->id
        ]);

        $user = Auth::user();
        $user->update([
            'name' => $request->get('name'),
            'username' => $request->get('username'),
            'password' => $request->get('password') ? Hash::make($request->get('password')) : $user->password
        ]);
        $this->setFlash('success', 'Profil Guncellendi');
        return Redirect::back();
    }

    private function setFlash($type, $message)
    {
        Session::flash('message', $message);
        Session::flash('type', $type);
    }


}
