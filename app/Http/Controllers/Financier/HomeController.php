<?php

namespace App\Http\Controllers\Financier;


use App\Http\Controllers\Controller;
use App\Models\FinancierAction;
use App\Models\FinancierNote;
use App\Models\Transaction;
use App\Repositories\TransactionActionRepository;
use App\Repositories\TransactionMethodRepository;
use App\Repositories\TransactionStatusRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
    private $transactionStatusRepository, $transactionsMethodRepository, $financierActionRepository;

    public function __construct()
    {
        $this->financierActionRepository = new TransactionActionRepository();
        $this->transactionStatusRepository = new TransactionStatusRepository();
        $this->transactionsMethodRepository = new TransactionMethodRepository();
    }

    public function index()
    {

        $notes = FinancierNote::where(function ($query) {
            return $query->whereNull('id_list')
                ->orWhereJsonContains('id_list', strval(Auth::id()));
        })->where(function ($query) {
            return $query->whereNull('read_list')
                ->orWhereJsonDoesntContain('read_list', strval(Auth::id()));
        })
            ->with('user')
            ->get();

        $data = [];

        $waitingStatus = $this->transactionStatusRepository->getByKey('waiting');

        $totalToday = $this->getTransactions($waitingStatus, 'today', null);

        $cancelledActions = $this->financierActionRepository->getByKey('transaction-cancelled');
        $withdrawCompleted = $this->financierActionRepository->getByKey('withdraw-transaction-completed');

        $financierCancelled = $this->getFinancierStats($cancelledActions, Auth::user());
        $financierWithdrawCompleted = $this->getFinancierStats($withdrawCompleted, Auth::user());


        $item = [
            'client_name' => 'Gunluk Degerler ',
            'client_id' => '',
            'statistics' => [
                [
                    'title' => 'Gunluk Toplam',
                    'data' => $this->getStats($totalToday),
                ],

                [
                    'title' => 'Havale ',
                    'data' => $this->getStats($totalToday),
                ],

                [
                    'title' => 'Papara ',
                    'data' => $this->getStats($totalToday),
                ],
            ]
        ];

        $itemCounts = [
            'client_name' => 'Genel Finansor Degerlendirmesi ',
            'client_id' => '',
            'statistics' => [
                [
                    'title' => 'Toplam Iptal Edilen',
                    'data' => $this->getStatsCounts($financierCancelled),
                ],

                [
                    'title' => 'Toplam Tamamlanan Cekim ',
                    'data' => $this->getStatsCounts($financierWithdrawCompleted),
                ],

            ]
        ];

        array_push($data, $itemCounts);
        array_push($data, $item);


        return view('financier.home.index')->with([
            'data' => $data,
            'notes' => $notes
        ]);
    }

    public function getFinancierStats(FinancierAction $financierAction, $financier)
    {
        return Transaction::whereHas('actions', function ($query) use ($financierAction, $financier) {
            return $query->where('action_id', $financierAction->id)
                ->where('financier_id', $financier->id);
        })
            ->get();

    }

    public function getTransactions($statusId, $date, $methodKey = null)
    {

        $transactionMethod = $this->transactionsMethodRepository->getByKey($methodKey);


        $transactions = Transaction::when($methodKey, function ($query) use ($transactionMethod) {
            return $query->where('method_id', $transactionMethod->id);
        })
            ->where('status_id', '!=', $statusId)
//            ->when($date === 'today', function ($query) {
//                return $query->whereDate('created_at', Carbon::today());
//            })
            ->when($date === 'month', function ($query) {
                return $query->whereMonth('created_at', date('m'))
                    ->whereYear('created_at', date('Y'));
            })
            ->get();


        return $transactions;

    }

    public function getStatsCounts($transactions)
    {

        return [
            [
                'title' => 'Adet',
                'bg' => 'bg-primary',
                'width' => 'col-sm-6',
                'data' => $transactions->count()
            ],
            [
                'title' => 'Miktar',
                'bg' => 'bg-primary',
                'width' => 'col-sm-6',
                'data' => $transactions->sum('amount'),
            ],
//            [
//                'title' => 'Yatirim Adet',
//                'bg' => 'bg-secondary',
//                'width' => 'col-sm-6',
//                'data' => $transactions->where('type_id', $this->depositStatus->id)->count(),
//            ],
//            [
//                'title' => 'Yatirim Miktar',
//                'bg' => 'bg-secondary',
//                'width' => 'col-sm-6',
//                'data' => $transactions->where('type_id', $this->depositStatus->id)->sum('amount'),
//            ],
//            [
//                'title' => 'Cekim Adet',
//                'bg' => 'bg-info',
//                'width' => 'col-sm-6',
//                'data' => $transactions->where('type_id', $this->withdrawStatus->id)->count(),
//            ],
//            [
//                'title' => 'Cekim Miktar',
//                'bg' => 'bg-info',
//                'width' => 'col-sm-6',
//                'data' => $transactions->where('type_id', $this->withdrawStatus->id)->sum('amount')
//            ],


        ];
    }

    public function getStats($transactions)
    {

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
//            [
//                'title' => 'Yatirim Adet',
//                'bg' => 'bg-secondary',
//                'width' => 'col-sm-6',
//                'data' => $transactions->where('type_id', $this->depositStatus->id)->count(),
//            ],
//            [
//                'title' => 'Yatirim Miktar',
//                'bg' => 'bg-secondary',
//                'width' => 'col-sm-6',
//                'data' => $transactions->where('type_id', $this->depositStatus->id)->sum('amount'),
//            ],
//            [
//                'title' => 'Cekim Adet',
//                'bg' => 'bg-info',
//                'width' => 'col-sm-6',
//                'data' => $transactions->where('type_id', $this->withdrawStatus->id)->count(),
//            ],
//            [
//                'title' => 'Cekim Miktar',
//                'bg' => 'bg-info',
//                'width' => 'col-sm-6',
//                'data' => $transactions->where('type_id', $this->withdrawStatus->id)->sum('amount')
//            ],


        ];
    }


    private function setFlash($type, $message)
    {
        Session::flash('message', $message);
        Session::flash('type', $type);
    }


}
