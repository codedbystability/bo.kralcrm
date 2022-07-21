<?php

namespace App\Http\Controllers\Client;

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

class TransactionController extends Controller
{

    private $transactionStatusRepository, $transactionTypeRepository, $transactionMethodRepository;

    public function __construct()
    {
        $this->transactionStatusRepository = new TransactionStatusRepository();
        $this->transactionTypeRepository = new TransactionTypeRepository();
        $this->transactionMethodRepository = new TransactionMethodRepository();
    }


    public function reports(Request $request)
    {
        $clients = Client::get();
        $types = $this->transactionTypeRepository->getAll();
        $methods = $this->transactionMethodRepository->getAll();
        $statutes = $this->transactionStatusRepository->getAll();
        $transactions = Transaction::where('client_id', Auth::id())
            ->orderBy('id', 'desc')
            ->with('website', 'client', 'status', 'type', 'method')
            ->paginate(20);

        $widgets = $this->getWidgets($transactions);


        return view('client.reports.index')->with([
            'data' => $transactions->appends($request->all()),
            'dateActive' => false,
            'widgets' => $widgets,
            'clients' => $clients,
            'statutes' => $statutes,
            'types' => $types,
            'websites' => Website::where('client_id', Auth::id())->get(),
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
            return $this->reports($request);
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
                    return $qq->where('name', $request->get('status_name'));
                });
            })
            ->when($request->get('type_name'), function ($query) use ($request) {
                return $query->whereHas('type', function ($qq) use ($request) {
                    return $qq->where('name', $request->get('type_name'));
                });
            })
            ->when($request->get('method_name'), function ($query) use ($request) {
                return $query->whereHas('method', function ($qq) use ($request) {
                    return $qq->where('name', $request->get('method_name'));
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
            'websites' => Website::where('client_id', Auth::id())->get(),
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
