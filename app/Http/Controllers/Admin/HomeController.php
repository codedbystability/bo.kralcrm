<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Financier;
use App\Models\Transaction;
use App\Models\TransactionStatus;
use App\Models\Website;
use App\Repositories\TransactionMethodRepository;
use App\Repositories\TransactionStatusRepository;
use App\Repositories\TransactionTypeRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    private $waitingStatus, $completedStatus, $canceledStatus;


    private $paparaMethod, $havaleMethod, $cancelledStatus, $withdrawStatus, $depositStatus, $transactionsMethodRepository, $transactionStatusRepository, $transactionTypeRepository;

    public function __construct()
    {
        $this->transactionsMethodRepository = new TransactionMethodRepository();
        $this->transactionStatusRepository = new TransactionStatusRepository();
        $this->transactionTypeRepository = new TransactionTypeRepository();


        $this->cancelledStatus = $this->transactionStatusRepository->getByKey('cancelled');

        $this->depositStatus = $this->transactionTypeRepository->getByKey('deposit');
        $this->withdrawStatus = $this->transactionTypeRepository->getByKey('withdraw');

    }

    public function index()
    {
        $data = [];

        $clients = Client::get();


        foreach ($clients as $client) {

            // client bazli gunluk deposit - withdraw - net
            // client bazli aylik deposit - withdraw - net

            $paparaToday = $this->getTransactions($client->id, 'today', 'papara');
            $paparaMonth = $this->getTransactions($client->id, 'month', 'papara');
            $havaleToday = $this->getTransactions($client->id, 'today', 'havale');
            $havaleMonth = $this->getTransactions($client->id, 'month', 'havale');

            $totalToday = $this->getTransactions($client->id, 'today', null);
            $totalMonth = $this->getTransactions($client->id, 'month', null);


            $item = [
                'client_name' => $client->name,
                'client_id' => $client->id,
                'statistics' => [
                    [
                        'title' => 'Gunluk Toplam',
                        'data' => $this->getStats($totalToday),

                    ],


                    [
                        'title' => 'Aylik Toplam',
                        'data' => $this->getStats($totalMonth),
                    ],
                    [
                        'title' => 'Papara Gunluk Veriler',
                        'data' => $this->getStats($paparaToday)
                    ],

                    [
                        'title' => 'Papara Aylik Veriler',
                        'data' => $this->getStats($paparaMonth)
                    ],


                    [
                        'title' => 'Havale Gunluk Veriler',
                        'data' => $this->getStats($havaleToday),
                    ],
                    [
                        'title' => 'Havale Aylik Veriler',
                        'data' => $this->getStats($havaleMonth),

                    ],
                ]
            ];

            array_push($data, $item);
        }


        return view('admin.home.index')->with([
            'data' => $data
        ]);
    }

    public function getTransactions($clientID, $date, $methodKey = null)
    {

        $transactionMethod = $this->transactionsMethodRepository->getByKey($methodKey);


        $transactions = Transaction::where('client_id', $clientID)
            ->when($methodKey, function ($query) use ($transactionMethod) {
                return $query->where('method_id', $transactionMethod->id);
            })
            ->where('status_id', '!=', $this->cancelledStatus->id)
            ->when($date === 'today', function ($query) {
                return $query->whereDate('created_at', Carbon::today());
            })
            ->when($date === 'month', function ($query) {
                return $query->whereMonth('created_at', date('m'))
                    ->whereYear('created_at', date('Y'));
            })
           // ->where('is_active', true)

            ->get();


        return $transactions;

    }

    public function getStats($transactions)
    {
        $net = $transactions->where('type_id', $this->depositStatus->id)->sum('amount') - $transactions->where('type_id', $this->withdrawStatus->id)->sum('amount');

        return [
            [
                'title' => 'Gunluk Adet',
                'bg' => 'bg-primary',
                'width' => 'col-sm-6',
                'data' => $transactions->count()
            ],
            [
                'title' => 'Gunluk Miktar',
                'bg' => 'bg-primary',
                'width' => 'col-sm-6',
                'data' => $transactions->sum('amount'),
            ],
            [
                'title' => 'Yatirim Adet',
                'bg' => 'bg-secondary',
                'width' => 'col-sm-6',
                'data' => $transactions->where('type_id', $this->depositStatus->id)->count(),
            ],
            [
                'title' => 'Yatirim Miktar',
                'bg' => 'bg-secondary',
                'width' => 'col-sm-6',
                'data' => $transactions->where('type_id', $this->depositStatus->id)->sum('amount'),
            ],
            [
                'title' => 'Cekim Adet',
                'bg' => 'bg-info',
                'width' => 'col-sm-6',
                'data' => $transactions->where('type_id', $this->withdrawStatus->id)->count(),
            ],
            [
                'title' => 'Cekim Miktar',
                'bg' => 'bg-info',
                'width' => 'col-sm-6',
                'data' => $transactions->where('type_id', $this->withdrawStatus->id)->sum('amount')
            ],


            [
                'title' => 'Net',
                'bg' => $net <= 0 ? 'bg-danger' : 'bg-success',
                'width' => 'col-sm-12',
                'data' => $net,
            ]

        ];
    }





    private function getRanges($all): array
    {
        $count = $all->count() >= 1 ? $all->count() : 1;
        return [
            'havale' => [
                'progress-color' => 'bg-primary',
                'label' => 'Havale Islemleri',
                'value' => $all->where('type_id', $this->havalePayment->id)->count(),
                'amount' => $all->where('type_id', $this->havalePayment->id)->sum('amount'),
                'width' => floor($all->where('type_id', $this->havalePayment->id)->count() / $count * 100)

            ],
            'papara' => [
                'progress-color' => 'bg-secondary',
                'label' => 'Papara Islemleri',
                'value' => $all->where('type_id', $this->paparaPayment->id)->count(),
                'amount' => $all->where('type_id', $this->paparaPayment->id)->sum('amount'),
                'width' => floor($all->where('type_id', $this->paparaPayment->id)->count() / $count * 100)

            ],

        ];
    }

    public function profile()
    {
        return view('admin.profile.index');
    }

    public function profileUpdate(Request $request)
    {
        $username = $request->get('username');
        $name = $request->get('name');
        $password = $request->get('password');

        $checkUsername = Financier::where('username', $username)
            ->where('id', '!=', Auth::id())
            ->first();


        if ($checkUsername) {
            $this->setFlash('error', 'Kullanici Adi Kullanilamaz');
            return Redirect::back();
        }

        $financier = Auth::user();
        $financier->update([
            'name' => $name,
            'username' => $username,
            'password' => $password ? Hash::make($password) : $financier->password
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
