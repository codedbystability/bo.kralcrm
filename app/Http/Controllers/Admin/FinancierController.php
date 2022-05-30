<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientFinancier;
use App\Models\Financier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class FinancierController extends Controller
{
    public function index()
    {
        $financiers = Financier::with('clients')->get();

        return view('admin.financiers.index')->with([
            'financiers' => $financiers
        ]);
    }

    public function create()
    {
        $permissions = $this->getAllPermissions();

        return view('admin.financiers.create')->with([
            'permissions' => $permissions
        ]);
    }

    public function store(Request $request)
    {
        $name = $request->get('name');
        $username = $request->get('username');
        $password = $request->get('password');

        $check = Financier::where('username', $username)->first();
        if ($check) {
            $this->setFlash('error', 'Kullanici Adi Kullanilamaz !');
            return Redirect::back();
        }

        $financier = Financier::create([
            'name' => $name,
            'username' => $username,
            'password' => Hash::make($password)
        ]);

        $permissionList = $request->get('permission_list');
        $permissionsIDs = array_map('intval', explode(',', $permissionList));

        $permissions = Permission::whereIn('id', $permissionsIDs)->get();

        $financierRole = Role::where('name', 'financier')->first();
        $financier->assignRole($financierRole);
        $financier->syncPermissions($permissions);


        $this->setFlash('success', 'Finansci Basariyla Olusturuldu !');
        return Redirect::route('admin.financiers.index');

    }

    public function edit(Request $request, $id)
    {
        $financier = Financier::find($id);

        if (!$financier) {
            $this->setFlash('error', 'Finansci Bulunamadi !');
            return Redirect::back();
        }
        $permissionArray = $financier->getAllPermissions()->pluck('id')->toArray();
        $permissions = $this->getAllPermissions();

        return view('admin.financiers.edit')->with([
            'permissions' => $permissions,
            'permissionArray' => $permissionArray,
            'financier' => $financier
        ]);
    }

    public function update(Request $request, $id)
    {
        $financier = Financier::find($id);
        if (!$financier) {
            $this->setFlash('error', 'Finansci Bulunamadi !');
            return Redirect::back();
        }
        $check = Financier::where('username', $request->get('username'))
            ->where('id', '!=', $id)
            ->first();
        if ($check) {
            $this->setFlash('error', 'Kullanici Adi Kullanilamaz !');
            return Redirect::back();
        }

        $financier->update([
            'username' => $request->get('username'),
            'name' => $request->get('name'),
        ]);
        $permissionList = $request->get('permission_list');
        $permissionsIDs = array_map('intval', explode(',', $permissionList));

        $permissions = Permission::whereIn('id', $permissionsIDs)->get();
        $financier->syncPermissions($permissions);

        return Redirect::route('admin.financiers.index');
    }

    public function destroy(Request $request, $id)
    {
        $financier = Financier::find($id);
        if (!$financier) {
            $this->setFlash('error', 'Finansci Bulunamadi !');
            return Redirect::back();
        }
        $financier->delete();

        $this->setFlash('success', 'Finansci Basariyla Silindi !');

        return Redirect::route('admin.financiers.index');

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


    public function storeCustomer(Request $request)
    {

        $financier = Financier::findOrFail($request->get('financier_id'));
        $clients = Client::whereIn('id', $request->get('clients'))->get();

        foreach ($clients as $client) {
            ClientFinancier::firstOrCreate([
                'client_id' => $client->id,
                'financier_id' => $financier->id
            ]);
        }
        $this->setFlash('success', 'Finansci Atamasi Yapildi !');
        return Redirect::route('admin.financiers.customers', $financier->id);


    }

    public function createCustomer($financierID)
    {
        $financier = Financier::findOrFail($financierID);
        $financierClients = ClientFinancier::where('financier_id', $financierID)
//            ->with('client')
            ->pluck('client_id')
            ->toArray();

        $allClients = Client::whereNotIn('id', $financierClients)->get();

        return view('admin.financiers.create-customer')->with([

            'clients' => $financierClients,
            'allClients' => $allClients,
            'financier' => $financier,
        ]);


    }

    public function customers($financierID)
    {
        $financier = Financier::findOrFail($financierID);
        $financierClients = ClientFinancier::where('financier_id', $financierID)
            ->with('client')
            ->get();

        return view('admin.financiers.index-customers')->with([
            'clients' => $financierClients,
            'financier' => $financier
        ]);
    }

    public function destroyCustomer($clientFinancierID)
    {
        $financierClient = ClientFinancier::findOrFail($clientFinancierID);
        $financierClient->delete();

        $this->setFlash('success', 'Finansci Atamasi Tamamlandi !');

        return Redirect::back();
    }

    private function setFlash($type, $message)
    {
        Session::flash('message', $message);
        Session::flash('type', $type);
    }

}
