<?php

namespace App\Http\Controllers\Client;

use App\Enums\TransactionStatusEnum;
use App\Enums\TransactionTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Transaction;
use App\Models\Website;
use App\Repositories\TransactionMethodRepository;
use App\Repositories\TransactionStatusRepository;
use App\Repositories\TransactionTypeRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{


    private $transactionStatusRepository, $transactionTypeRepository, $transactionMethodRepository;

    public function __construct()
    {
        $this->transactionStatusRepository = new TransactionStatusRepository();
        $this->transactionTypeRepository = new TransactionTypeRepository();
        $this->transactionMethodRepository = new TransactionMethodRepository();
    }


    public function index(Request $request)
    {
        $clients = Client::get();
        $types = $this->transactionTypeRepository->getAll();
        $methods = $this->transactionMethodRepository->getAll();
        $statutes = $this->transactionStatusRepository->getAll();
        $transactions = Transaction::where('client_id', Auth::id())
            ->with('website', 'client', 'status', 'type', 'method')->paginate(20);
        $widgets = $this->getWidgets($transactions);


        return view('client.reports.index')->with([
            'data' => $transactions->appends($request->all()),
            'dateActive' => false,
            'widgets' => $widgets,
            'clients' => $clients,
            'statutes' => $statutes,
            'types' => $types,
            'websites' => Website::where('client_id',Auth::id())->get(),
            'methods' => $methods,

            'client_name' => null,
            'website_name' => null,
            'status_name' => null,
            'type_name' => null,
            'method_name' => null,
            'min_amount' => null,
            'max_amount' => null,
            'date' => Carbon::now()->format('d-m-Y') . ' / ' . Carbon::now()->format('d-m-Y')
        ]);
    }

    public function filter(Request $request)
    {

        if ($request->get('clear') && $request->get('clear') === 'clear-filter') {
            return $this->index($request);
        }

        $today = Carbon::now()->format('d-m-Y') . ' 00:00:00';

        if ($request->get('date_range')) {
            $dateRange = $request->get('date_range');
            $dates = explode(' / ', $dateRange);
            $fromDate = $dates[0] . ' 00:00:00';
            $toDate = $dates[1] . ' 00:00:00';
        } else {
            $toDate = $today;
            $fromDate = $today;
        }


        $transactions = Transaction::where('client_id', Auth::id())
            ->when($request->get('website_name'), function ($query) use ($request) {
                return $query->whereHas('website', function ($qq) use ($request) {
                    return $qq->where('domain', $request->get('website_name'));
                });
            })
            ->when($request->get('status_name'), function ($query) use ($request) {
                return $query->whereHas('status', function ($qq) use ($request) {
                    return $qq->where('key', $request->get('status_name'));
                });
            })
            ->when($request->get('type_name'), function ($query) use ($request) {
                return $query->whereHas('type', function ($qq) use ($request) {
                    return $qq->where('key', $request->get('type_name'));
                });
            })
            ->when($request->get('method_name'), function ($query) use ($request) {
                return $query->whereHas('method', function ($qq) use ($request) {
                    return $qq->where('key', $request->get('method_name'));
                });
            })
            ->when($request->get('min_amount'), function ($query) use ($request) {
                return $query->where('amount', '>=', $request->get('min_amount'));
            })
            ->when($request->get('max_amount'), function ($query) use ($request) {
                return $query->where('amount', '<=', $request->get('max_amount'));
            })
            ->when(!($fromDate === $toDate && $toDate === $today), function ($query) use ($fromDate, $toDate) {
                return $query->whereDate('created_at', ">=", Carbon::createFromFormat('d-m-Y H:i:s', $fromDate))
                    ->whereDate('created_at', "<=", Carbon::createFromFormat('d-m-Y H:i:s', $toDate));

            })
            ->with('website', 'client', 'status', 'type', 'method')
            ->paginate(20);


        $clients = Client::get();
        $types = $this->transactionTypeRepository->getAll();
        $methods = $this->transactionMethodRepository->getAll();
        $statutes = $this->transactionStatusRepository->getAll();

        $widgets = $this->getWidgets($transactions);


        return view('client.reports.index')->with([
            'data' => $transactions->appends($request->all()),
            'dateActive' => !($fromDate === $toDate && $toDate === $today),
            'widgets' => $widgets,
            'clients' => $clients,
            'statutes' => $statutes,
            'types' => $types,
            'websites' => Website::where('client_id',Auth::id())->get(),
            'methods' => $methods,

            'client_name' => $request->get('client_name'),
            'website_name' => $request->get('website_name'),
            'status_name' => $request->get('status_name'),
            'type_name' => $request->get('type_name'),
            'method_name' => $request->get('method_name'),
            'min_amount' => $request->get('min_amount'),
            'max_amount' => $request->get('max_amount'),

            'date' => $request->get('date_range')
        ]);

    }

    private function getTransactionableTitle($key)
    {
        switch ($key) {
            case 'bank':
                return 'Bank';
            case 'fullname':
                return 'Ad Soyad';
            case 'id':
                return 'Islem ID';
            case 'created_at':
                return 'Islem Tarihi';
            case 'updated_at':
                return 'Son Islem Tarihi';
            case 'accno':
                return 'Hesap No';
            case 'iban':
                return 'Iban Adresi';
            case 'owner':
                return 'Hesap Sahibi';
            case 'branch':
                return 'Sube';
            case 'region':
                return 'Bolge';
            case 'identity':
                return 'Kimlik No';
            case 'identity_date':
                return 'Kimlik Verilis Tarihi';
        }
    }

    public function detail(Request $request, $id)
    {
        $transaction = Transaction::with([
            'transactionable',
            'client',
            'status',
            'type',
            'method',
            'website',
            'account.accountable',
            'notes.financier'
        ])
            ->find($id);


        $transactionable = $transaction->transactionable->toArray();

        $transactionRequest = [
            [
                'class' => 'col-lg-12',
                'title' => 'Website',
                'key' => 'website',
                'value' => $transaction->website->domain,
            ],

        ];
        if ($transaction->account) {
            $bankInfo = [
                [
                    'title' => 'Hesap Sahibi',
                    'key' => 'owner',
                    'value' => $transaction->account->accountable->owner,
                    'class' => 'col-lg-6'
                ],

                [
                    'title' => 'Hesap No',
                    'key' => 'accno',
                    'value' => $transaction->account->accountable->accno,
                    'class' => 'col-lg-6'
                ],

                [
                    'title' => 'Min. Yatirim',
                    'value' => $transaction->account->accountable->min_deposit,
                    'class' => 'col-lg-3'
                ],

                [
                    'title' => 'Maks. Yatirim',
                    'value' => $transaction->account->accountable->max_deposit,
                    'class' => 'col-lg-3'
                ],

                [
                    'title' => 'Min. Cekim',
                    'value' => $transaction->account->accountable->min_withdraw,
                    'class' => 'col-lg-3'
                ],


                [
                    'title' => 'Maks. Cekim',
                    'value' => $transaction->account->accountable->max_withdraw,
                    'class' => 'col-lg-3'
                ],
            ];
        } else {
            $bankInfo = null;
        }
        $index = 0;
        foreach ($transactionable as $key => $item) {


            array_push($transactionRequest,
                [
                    'key' => $key,
                    'class' => $index >= 3 ? 'col-lg-6' : 'col-lg-6',
                    'value' => $item,
                    'title' => $this->getTransactionableTitle($key)
                ]);
            $index++;
        }


        $widgets = [
            [
                'title' => 'Method',
                'key' => 'method',
                'value' => $transaction->method->name,
                'bg' => 'bg-primary',
            ],
            [
                'title' => 'Miktar',
                'key' => 'amount',
                'value' => $transaction->amount,
                'bg' => 'bg-light',

            ],

            [
                'title' => 'Tip',
                'key' => 'type',
                'value' => TransactionTypeEnum::get($transaction->type->key),
                'bg' => 'bg-dark',
            ],

            [
                'title' => 'Durum',
                'key' => 'status',
                'value' => TransactionStatusEnum::get($transaction->status->key),
                'bg' => 'bg-info',
            ],


        ];


        return view('client.reports.detail')->with([
            'transaction' => $transaction,
            'widgets' => $widgets,
            'request' => $transactionRequest,
            'bank_info' => $transaction->type->key === 'deposit' ? $bankInfo : null
        ]);
    }

    public function getWebsites(Request $request): \Illuminate\Http\JsonResponse
    {

        $websites = Website::whereHas('client', function ($query) use ($request) {
            return $query->where('name', $request->get('client_name'));
        })->get();
        return response()->json([
            'websites' => $websites,
            'status' => 200
        ]);
    }

    private function getWidgets($transactions)
    {
        return [
            [
                'title' => 'Toplam Hacim',
                'value' => $transactions->count(),
                'subValue' => $transactions->sum('amount'),
                'bg' => 'bg-danger',
                'icon' => 'fa-shopping-cart',
            ],
            [
                'title' => 'Toplam Yatirim',
                'value' => $transactions->where('type_id', $this->transactionTypeRepository->getByKey('deposit')->id)->count(),
                'subValue' => $transactions->where('type_id', $this->transactionTypeRepository->getByKey('deposit')->id)->sum('amount'),
                'bg' => 'bg-primary',
                'icon' => 'fa-plus-square',

            ],
            [
                'title' => 'Toplam Cekim',
                'value' => $transactions->where('type_id', $this->transactionTypeRepository->getByKey('withdraw')->id)->count(),
                'subValue' => $transactions->where('type_id', $this->transactionTypeRepository->getByKey('withdraw')->id)->sum('amount'),
                'bg' => 'bg-secondary',
                'icon' => 'fa-minus-square',

            ],

            [
                'title' => 'Net',
                'value' => 0,
                'subValue' => $transactions->where('type_id', $this->transactionTypeRepository->getByKey('deposit')->id)->sum('amount') - $transactions->where('type_id', $this->transactionTypeRepository->getByKey('withdraw')->id)->sum('amount'),
                'bg' => 'bg-success',
                'icon' => 'fa-money-bill'

            ],
        ];
    }


}
