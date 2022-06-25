<?php

namespace App\Http\Controllers\Financier;

use App\Http\Controllers\Controller;
use App\Models\ClientFinancier;
use App\Models\PaparaAccount;
use App\Models\Transaction;
use App\Repositories\TransactionMethodRepository;
use App\Repositories\TransactionStatusRepository;
use App\Repositories\TransactionTypeRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HavaleTransactionController extends Controller
{
    private $havaleMethod, $transactionTypeRepository, $transactionStatusRepository;

    public function __construct()
    {
        $transactionMethodRepository = new TransactionMethodRepository();
        $this->havaleMethod = $transactionMethodRepository->getByKey('havale');

        $this->transactionStatusRepository = new TransactionStatusRepository();
        $this->transactionTypeRepository = new TransactionTypeRepository();
    }

    public function waitingWithdraws()
    {
        $permissionKey = 'havale withdraw waiting';

        return $this->handleDynamicIndexData('waiting', 'withdraw', 'Havale Bekleyen Cekimler', $permissionKey);
    }

    public function waitingDeposits()
    {
        $permissionKey = 'havale deposit waiting';

        return $this->handleDynamicIndexData('waiting', 'deposit', 'Havale Bekleyen Yatirimlar', $permissionKey);
    }

    public function waitingDepositsApproveChecked()
    {
        $permissionKey = 'havale deposit waiting';

        return $this->handleDynamicIndexData('waiting', 'deposit', 'Havale Bekleyen Yatirimlar', $permissionKey, true);
    }

    public function completedWithdraws()
    {
        $permissionKey = 'havale deposit completed';

        return $this->handleDynamicIndexData('completed', 'withdraw', 'Havale Tamamlanan Cekimler', $permissionKey);
    }

    public function cancelledWithdraws()
    {
        $permissionKey = 'havale withdraw cancelled';

        return $this->handleDynamicIndexData('cancelled', 'withdraw', 'Havale Iptal Edilen Cekimler', $permissionKey);
    }

    public function cancelledDeposits()
    {
        $permissionKey = 'havale deposit cancelled';

        return $this->handleDynamicIndexData('cancelled', 'deposit', 'Havale Iptal Edilen Yatirimlar', $permissionKey);
    }


    public function approvedDeposits()
    {
        $permissionKey = 'havale deposit approved';

        return $this->handleDynamicIndexData('approved', 'deposit', 'Havale Onaylanan Yatirimlar', $permissionKey);
    }

    public function completedDeposits()
    {
        $permissionKey = 'havale deposit completed';

        return $this->handleDynamicIndexData('completed', 'deposit', 'Havale Tamamlanan Yatirimlar', $permissionKey);
    }

    public function approvedWithdraws()
    {
        $permissionKey = 'papara deposit approved';

        return $this->handleDynamicIndexData('approved', 'withdraw', 'Onaylanan Cekimler', $permissionKey);
    }

    private function handleDynamicIndexData($statusKey, $typeKey, $title, $permissionKey, $approveChecked = false)
    {
        $clientIds = ClientFinancier::where('financier_id', Auth::id())->pluck('client_id')->toArray();

        $status = $this->transactionStatusRepository->getByKey($statusKey);
        $approvedStatus = $this->transactionStatusRepository->getByKey('approved');
        $type = $this->transactionTypeRepository->getByKey($typeKey);

        $transactions = Transaction::whereIn('client_id', $clientIds)
            ->when($approveChecked === true, function ($qq) {
                return $qq->where('check_performed_by_client', true);
            })
            ->when($statusKey === 'waiting' && $typeKey === 'deposit', function ($q) use ($status, $approvedStatus) {
                return $q->where(function ($qqq) use ($status, $approvedStatus) {
                    return $qqq->where('status_id', $status->id)
                        ->orWhere('status_id', $approvedStatus->id);
                });
            }, function ($query) use ($status, $approvedStatus) {
                return $query->where('status_id', $status->id);
            })

//            ->where('status_id', $status->id)
            ->where('method_id', $this->havaleMethod->id)
            ->where('type_id', $type->id)
            ->where('is_active', true)
            ->select(['id', 'client_id', 'is_active', 'direct_approve', 'currency_code', 'status_id', 'type_id', 'method_id', 'account_id', 'amount', 'approved_amount', 'transactionable_id', 'transactionable_type', 'edit_time'])
            ->with('transactionable')
            ->with(['status' => function ($query) {
                return $query->select('id', 'name', 'key');
            }, 'type' => function ($query) {
                return $query->select('id', 'name', 'key');
            }, 'method' => function ($query) {
                return $query->select('id', 'name', 'key');
            }, 'client' => function ($query) {
                return $query->select('id', 'name', 'username');
            }])
            ->orderBy('id', 'desc')
            ->paginate(20);


        return view('financier.transactions.index')->with([
            'transactions' => $transactions,
            'permissionKey' => $permissionKey,
            'title' => $title
        ]);

    }
}
