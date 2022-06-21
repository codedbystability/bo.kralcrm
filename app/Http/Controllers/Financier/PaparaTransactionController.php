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

class PaparaTransactionController extends Controller
{
    private $paparaMethod, $transactionTypeRepository, $transactionStatusRepository;

    public function __construct()
    {
        $transactionMethodRepository = new TransactionMethodRepository();
        $this->paparaMethod = $transactionMethodRepository->getByKey('papara');

        $this->transactionStatusRepository = new TransactionStatusRepository();
        $this->transactionTypeRepository = new TransactionTypeRepository();
    }

    public function waitingWithdraws()
    {
        $permissionKey = 'papara withdraw waiting';

        return $this->handleDynamicIndexData('waiting', 'withdraw', 'Papara Bekleyen Cekimler', $permissionKey);
    }

    public function waitingDeposits()
    {
        $permissionKey = 'papara deposit waiting';

        return $this->handleDynamicIndexData('waiting', 'deposit', 'Papara Bekleyen Yatirimlar', $permissionKey);
    }

    public function waitingDepositsApproveChecked()
    {
        $permissionKey = 'papara deposit waiting';

        return $this->handleDynamicIndexData('waiting', 'deposit', 'Papara Bekleyen Yatirimlar', $permissionKey, true);
    }

    public function completedWithdraws()
    {
        $permissionKey = 'papara withdraw completed';

        return $this->handleDynamicIndexData('completed', 'withdraw', 'Tamamlanan Cekimler', $permissionKey);
    }

    public function cancelledWithdraws()
    {
        $permissionKey = 'papara withdraw cancelled';

        return $this->handleDynamicIndexData('cancelled', 'withdraw', 'Iptal Edilen Cekimler', $permissionKey);
    }

    public function cancelledDeposits()
    {
        $permissionKey = 'papara deposit cancelled';

        return $this->handleDynamicIndexData('cancelled', 'deposit', 'Iptal Edilen Yatirimlar', $permissionKey);
    }


    public function approvedDeposits()
    {

        $permissionKey = 'papara deposit approved';

        return $this->handleDynamicIndexData('approved', 'deposit', 'Onaylanan Yatirimlar', $permissionKey);
    }

    public function completedDeposits()
    {
        $permissionKey = 'papara deposit completed';

        return $this->handleDynamicIndexData('completed', 'deposit', 'Tamamlanan Yatirimlar', $permissionKey);
    }


    private function handleDynamicIndexData($statusKey, $typeKey, $title, $permissionKey, $approveChecked = false)
    {
        $clientIds = ClientFinancier::where('financier_id', Auth::id())->pluck('client_id')->toArray();

        $status = $this->transactionStatusRepository->getByKey($statusKey);
        $type = $this->transactionTypeRepository->getByKey($typeKey);

        $approvedStatus = $this->transactionStatusRepository->getByKey('approved');

        $transactions = Transaction::whereIn('client_id', $clientIds)
            ->when($approveChecked === true, function ($qq) {
                return $qq->where('check_performed_by_client', true);
            })
            ->when($statusKey === 'waiting' && $typeKey === 'deposit', function ($q) use ($status, $approvedStatus) {
                return $q->where('status_id', $status->id)
                    ->orWhere('status_id', $approvedStatus->id);
            }, function ($query) use ($status, $approvedStatus) {
                return $query->where('status_id', $status->id);
            })

//            ->where('status_id', $status->id)
            ->where('method_id', $this->paparaMethod->id)
            ->where('type_id', $type->id)
            ->where('is_active', true)
            ->with('transactionable')
            ->select(['id', 'client_id', 'direct_approve', 'is_active', 'currency_code', 'status_id', 'type_id', 'method_id', 'account_id', 'amount', 'approved_amount', 'transactionable_id', 'transactionable_type', 'edit_time'])
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
