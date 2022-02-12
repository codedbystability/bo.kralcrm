<?php

namespace App\Http\Controllers\Client;


use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TransactionStatus;
use App\Models\Website;
use App\Repositories\TransactionMethodRepository;
use App\Repositories\TransactionStatusRepository;
use App\Repositories\TransactionTypeRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
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

        $client = Auth::user();
        $websites = Website::where('client_id', $client->id)->get();

        foreach ($websites as $website) {

            $paparaToday = $this->getTransactions($website->id, 'today', 'papara');
            $paparaMonth = $this->getTransactions($website->id, 'month', 'papara');
            $havaleToday = $this->getTransactions($website->id, 'today', 'havale');
            $havaleMonth = $this->getTransactions($website->id, 'month', 'havale');

            $totalToday = $this->getTransactions($website->id, 'today', null);
            $totalMonth = $this->getTransactions($website->id, 'month', null);


            $item = [
                'website_domain' => $website->domain,
                'website_id' => $website->id,
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

        return view('client.home.index')->with([
            'data' => $data
        ]);
    }

    public function getTransactions($websiteID, $date, $methodKey = null)
    {

        $transactionMethod = $this->transactionsMethodRepository->getByKey($methodKey);

        return Transaction::where('website_id', $websiteID)
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
            ->get();

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


    private function setFlash($type, $message)
    {
        Session::flash('message', $message);
        Session::flash('type', $type);
    }


}
