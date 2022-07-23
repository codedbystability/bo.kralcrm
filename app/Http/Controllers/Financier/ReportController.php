<?php

namespace App\Http\Controllers\Financier;

use App\Enums\TransactionStatusEnum;
use App\Enums\TransactionTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\BankAccount;
use App\Models\Client;
use App\Models\Currency;
use App\Models\HavaleDeposit;
use App\Models\HavaleWithdraw;
use App\Models\PaparaDeposit;
use App\Models\PaparaWithdraw;
use App\Models\Transaction;
use App\Models\Website;
use App\Repositories\BankAccountRepository;
use App\Repositories\TransactionMethodRepository;
use App\Repositories\TransactionStatusRepository;
use App\Repositories\TransactionTypeRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ReportController extends Controller
{


    private $transactionStatusRepository, $transactionTypeRepository, $transactionMethodRepository, $bankAccountRepository, $currencies;

    public function __construct()
    {
        $this->transactionStatusRepository = new TransactionStatusRepository();
        $this->transactionTypeRepository = new TransactionTypeRepository();
        $this->transactionMethodRepository = new TransactionMethodRepository();
        $this->currencies = Currency::select('id', 'code')->get();
    }


    public function index(Request $request)
    {
        $clients = Client::get();
        $types = $this->transactionTypeRepository->getAll();
        $methods = $this->transactionMethodRepository->getAll();
        $statutes = $this->transactionStatusRepository->getAll();
        $dateFrom = Carbon::now()->subMonth(1)->format('Y-m-d');
        $dateTo = Carbon::now()->format('Y-m-d');

        $transactions = Transaction::with('website', 'client', 'status', 'type', 'method', 'currency','transactionable')
            ->whereDate('created_at', ">=", Carbon::createFromFormat('Y-m-d', $dateFrom))
            ->whereDate('created_at', "<=", Carbon::createFromFormat('Y-m-d', $dateTo))
            ->orderBy('id', 'desc')
            ->paginate(20);


        $widgets = $this->getWidgets($transactions);

        return view('financier.reports.index')->with([
            'data' => $transactions->appends($request->all()),
            'dateActive' => false,

            'widgets' => $widgets,
            'clients' => $clients,
            'statutes' => $statutes,
            'types' => $types,
            'websites' => [],
            'methods' => $methods,
            'currencies' => $this->currencies,

            'customer_name' => null,
            'currency_name' => null,
            'client_name' => null,
            'website_name' => null,
            'status_name' => null,
            'type_name' => null,
            'method_name' => null,
            'min_amount' => null,
            'max_amount' => null,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo
        ]);
    }

    public function accounts(Request $request)
    {
        $banks = Bank::select('id', 'name')->get();

        $dateFrom = Carbon::now()->subDays(7)->format('Y-m-d');
        $dateTo = Carbon::now()->format('Y-m-d');
        $clients = Client::get();
        $types = $this->transactionTypeRepository->getAll();
        $methods = $this->transactionMethodRepository->getAll();
        $statutes = $this->transactionStatusRepository->getAll();
        $transactions = Transaction::with('website', 'client', 'status', 'type', 'method', 'currency')
            ->whereDate('created_at', ">=", Carbon::createFromFormat('Y-m-d', $dateFrom))
            ->whereDate('created_at', "<=", Carbon::createFromFormat('Y-m-d', $dateTo))
            ->orderBy('id', 'desc')
            ->paginate(20);
        $widgets = $this->getWidgets($transactions);

        return view('financier.reports.accounts')->with([
            'banks' => $banks,
            'currencies' => $this->currencies,
            'bank_name' => null,
            'currency_name' => null,
            'data' => $transactions,
            'widgets' => $widgets,

            'dateActive' => false,
            'clients' => $clients,
            'statutes' => $statutes,
            'types' => $types,
            'websites' => [],
            'bankAccounts' => [],
            'methods' => $methods,
            'account_id' => null,
            'client_name' => null,
            'website_name' => null,
            'status_name' => null,
            'type_name' => null,
            'method_name' => null,
            'min_amount' => null,
            'max_amount' => null,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo
        ]);
    }

    public function accountsFilter(Request $request)
    {
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $banks = Bank::select('id', 'name')->get();


        $transactions = Transaction::when($request->get('website_name'), function ($query) use ($request) {
            return $query->whereHas('website', function ($qq) use ($request) {
                return $qq->where('domain', $request->get('website_name'));
            });
        })
            ->when($request->get('bank_name'), function ($query) use ($request) {
                return $query->whereHas('account', function ($qq) use ($request) {
                    $bank = Bank::where('name', $request->get('bank_name'))->first();

                    return $qq->whereHasMorph('accountable', BankAccount::class, function ($query) use ($bank) {
                        return $query->where('bank_id', $bank->id);
                    });

//                    where('key', $request->get('status_name'));
                });
            })
            ->when($request->get('account_id'), function ($query) use ($request) {
                return $query->whereHas('account', function ($qq) use ($request) {
                    return $qq->whereHasMorph('accountable', BankAccount::class, function ($query) use ($request) {
                        return $query->where('accountable_id', $request->get('account_id'));
                    });

//                    where('key', $request->get('status_name'));
                });
                return $query->where('account_id', $request->get('account_id'));
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
            ->whereDate('created_at', ">=", Carbon::createFromFormat('Y-m-d', $dateFrom))
            ->whereDate('created_at', "<=", Carbon::createFromFormat('Y-m-d', $dateTo))
            ->with('website', 'client', 'status', 'type', 'method', 'currency')
            ->orderBy('id', 'desc')
            ->paginate(20)
            ->appends($request->except('page'));


        $clients = Client::get();
        $types = $this->transactionTypeRepository->getAll();
        $methods = $this->transactionMethodRepository->getAll();
        $statutes = $this->transactionStatusRepository->getAll();

        $widgets = $this->getWidgets($transactions);


        return view('financier.reports.accounts')->with([
            'data' => $transactions->appends($request->all()),
            'dateActive' => true,
            'widgets' => $widgets,
            'clients' => $clients,
            'statutes' => $statutes,
            'types' => $types,
            'banks' => $banks,
            'websites' => [],
            'bankAccounts' => $request->get('bank_name') ? BankAccount::whereHas('bank', function ($query) use ($request) {
                return $query->where('name', $request->get('bank_name'));
            })
                ->with('currency')
                ->select('id', 'iban', 'branch', 'currency_id')
                ->get() : [],
            'methods' => $methods,
            'currencies' => $this->currencies,
            'currency_name' => $request->get('currency_name'),
            'client_name' => $request->get('client_name'),
            'website_name' => $request->get('website_name'),
            'status_name' => $request->get('status_name'),
            'type_name' => $request->get('type_name'),
            'method_name' => $request->get('method_name'),
            'min_amount' => $request->get('min_amount'),
            'max_amount' => $request->get('max_amount'),
            'account_id' => $request->get('account_id'),
            'bank_name' => $request->get('bank_name'),
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo
        ]);
    }

    public function filter(Request $request)
    {

        if ($request->get('clear') && $request->get('clear') === 'clear-filter') {
            return $this->index($request);
        }

        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');


        $transactions = Transaction::when($request->get('website_name'), function ($query) use ($request) {
            return $query->whereHas('website', function ($qq) use ($request) {
                return $qq->where('domain', $request->get('website_name'));
            });
        })
            ->has('transactionable')
            ->with('website', 'client', 'status', 'type', 'method', 'currency','transactionable')

            ->whereDate('created_at', ">=", Carbon::createFromFormat('Y-m-d H:i:s', $dateFrom . ' 00:00:00'))
            ->whereDate('created_at', "<=", Carbon::createFromFormat('Y-m-d H:i:s', $dateTo . ' 23:59:59'))
            ->orderBy('id', 'desc')

            ->when($request->get('customer_name'), function ($query) use ($request) {
                // ISIMLE ARAMA geldiginde
                return  $query->whereHas('transactionable', [PaparaDeposit::class, HavaleDeposit::class,HavaleWithdraw::class], function($query) use ($request){
                    return $query->where('fullname', 'like', '%' . $request->get('customer_name') . '%')->get();
                });

            })
            ->when($request->get('currency_name'), function ($query) use ($request) {
                return $query->whereHas('currency', function ($qq) use ($request) {
                    return $qq->where('code', $request->get('currency_name'));
                });
            })
            ->when($request->get('client_name'), function ($query) use ($request) {
                return $query->whereHas('client', function ($qq) use ($request) {
                    return $qq->where('name', $request->get('client_name'));
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


            ->paginate(20)
            ->appends($request->except('page'));


//        dd($request->all(),$transactions);
        $clients = Client::get();
        $types = $this->transactionTypeRepository->getAll();
        $methods = $this->transactionMethodRepository->getAll();
        $statutes = $this->transactionStatusRepository->getAll();

        $widgets = $this->getWidgets($transactions);


        return view('financier.reports.index')->with([
            'data' => $transactions->appends($request->all()),
            'dateActive' => true,
            'widgets' => $widgets,
            'clients' => $clients,
            'statutes' => $statutes,
            'types' => $types,
            'websites' => [],
            'methods' => $methods,
            'currencies' => $this->currencies,
            'currency_name' => $request->get('currency_name'),
            'customer_name' => $request->get('customer_name'),
            'client_name' => $request->get('client_name'),
            'website_name' => $request->get('website_name'),
            'status_name' => $request->get('status_name'),
            'type_name' => $request->get('type_name'),
            'method_name' => $request->get('method_name'),
            'min_amount' => $request->get('min_amount'),
            'max_amount' => $request->get('max_amount'),
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo
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


        return view('financier.reports.detail')->with([
            'transaction' => $transaction,
            'widgets' => $widgets,
            'request' => $transactionRequest,
            'bank_info' => $transaction->type->key === 'deposit' ? $bankInfo : null
        ]);
    }

    public function detailInList(Request $request, $id)
    {

        $this->setFlash('successs', 'Islem bulunamadi !');


        return redirect()->back();
        $transactions = Transaction::with('client', 'method', 'status', 'type', 'transactionable')
            ->where('id', $id)->get();

        $permissionKey = '';
        $title = '';
        return view('financier.transactions.index2')->with([
            'transactions' => $transactions,
            'permissionKey' => '$permissionKey',
            'title' => '$title'
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

    private function getWidgets($transactions): array
    {
        return [
            [
                'title' => 'Toplam',
                'value' => $transactions->count(),
                'subValue' => $transactions->sum('amount'),
                'bg' => 'bg-danger',
                'icon' => 'fa-shopping-cart',
            ],
            [
                'title' => 'Yatirim',
                'value' => $transactions->where('type_id', $this->transactionTypeRepository->getByKey('deposit')->id)->count(),
                'subValue' => $transactions->where('type_id', $this->transactionTypeRepository->getByKey('deposit')->id)->sum('amount'),
                'bg' => 'bg-primary',
                'icon' => 'fa-plus-square',

            ],
            [
                'title' => 'Cekim',
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

    private function setFlash($type, $message)
    {
        Session::flash('message', $message);
        Session::flash('type', $type);
    }

}
