<?php

namespace App\Http\Controllers\Financier;

use App\Http\Controllers\Controller;
use App\Jobs\InformClientJob;
use App\Models\Account;
use App\Models\Bank;
use App\Models\BankAccount;
use App\Models\ClientAccountAgreement;
use App\Models\ClientFinancier;
use App\Models\Note;
use App\Models\PaparaAccount;
use App\Models\Transaction;
use App\Repositories\TransactionActionRepository;
use App\Repositories\TransactionStatusRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class TransactionController extends Controller
{

    private $transactionStatusRepository, $transactionActionRepository;

    public function __construct()
    {
        $this->transactionStatusRepository = new TransactionStatusRepository();
        $this->transactionActionRepository = new TransactionActionRepository();
    }

    public function complete(Request $request, $id): \Illuminate\Http\RedirectResponse
    {

        $transaction = Transaction::with('client', 'method', 'status', 'type', 'transactionable')->find($request->get('complete_transaction_id'));

        if (!$transaction || $transaction->type->key !== 'deposit') {
            $this->setFlash('error', 'Islem bulunamadi !');
            return Redirect::back();
        }
        $editTime = $transaction->edit_time;
        $statusId = $transaction->status_id;

        $oldTransaction = $transaction;
        $completedStatus = $this->transactionStatusRepository->getByKey('completed');
        $transaction->update([
            'status_id' => $completedStatus->id,
            'approved_amount' => $request->get('approved_amount'),
            'edit_time' => Carbon::now()
        ]);
        $this->transactionActionRepository->action(Auth::user(), $transaction, $oldTransaction->type->key === 'withdraw' ? 'withdraw-transaction-completed' : 'deposit-transaction-completed');

        InformClientJob::dispatch($transaction, 'S', $editTime, $statusId)->onQueue('information_queue');
        $this->setFlash('success', 'Islem Tamamladi !');
        return $this->setReturnPage($oldTransaction);
    }

    public function approve(Request $request, $id): \Illuminate\Http\RedirectResponse
    {
        $transaction = Transaction::with('client', 'method', 'status', 'type', 'transactionable')->find($id);

        if (!$transaction) {
            $this->setFlash('error', 'Islem bulunamadi !');
            return Redirect::back();
        }

        $oldTransaction = $transaction;
        $approvedStatus = $this->transactionStatusRepository->getByKey($transaction->type->key === 'deposit' ? 'approved' : 'completed');
        $transaction->update(['status_id' => $approvedStatus->id]);
        $this->transactionActionRepository->action(Auth::user(), $transaction, $transaction->type->key === 'deposit' ? 'deposit-bank-info-send' : 'withdraw-transaction-completed');
        $this->setFlash('success', 'Islem Onaylandi !');

        return $this->setReturnPage($oldTransaction);

    }

    public function directApprove(Request $request, $id): \Illuminate\Http\RedirectResponse
    {
        $transaction = Transaction::with('client', 'method', 'status', 'type', 'transactionable')->find($id);

        if (!$transaction || $transaction->status->key !== 'completed') {
            $this->setFlash('error', 'Islem bulunamadi !');
            return Redirect::back();
        }

        $editTime = Carbon::now();
        $oldTransaction = $transaction;
        $completedStatus = $this->transactionStatusRepository->getByKey('completed');
        $transaction->update([
            'direct_approve' => true,
            'status_id' => $completedStatus->id,
            'approved_amount' => $request->get('approved_amount'),
            'edit_time' => $editTime
        ]);
        $statusId = $transaction->status_id;
        $this->transactionActionRepository->action(Auth::user(), $transaction, $oldTransaction->type->key === 'withdraw' ? 'withdraw-transaction-completed' : 'deposit-transaction-completed');

        InformClientJob::dispatch($transaction, 'S', $editTime, $statusId)->onQueue('information_queue');


        $this->setFlash('success', 'Islem Dogrudan Onaylandi !');
        return Redirect::back();
    }

    public function cancel(Request $request): \Illuminate\Http\RedirectResponse
    {
        $cancelledStatus = $this->transactionStatusRepository->getByKey('cancelled');
        $transaction = Transaction::with('client', 'method', 'status', 'type', 'transactionable')
            ->find($request->get('transaction_to_cancel'));

        if (!$transaction) {
            $this->setFlash('error', 'Islem bulunamadi !');
            return Redirect::back();
        }

        $editTime = $transaction->edit_time;
        $statusId = $transaction->status_id;

        $oldTransaction = $transaction;
        Note::create([
            'transaction_id' => $transaction->id,
            'financier_id' => Auth::id(),
            'message' => $request->get('message')
        ]);

        $transaction->update([
            'status_id' => $cancelledStatus->id,
            'edit_time' => Carbon::now()
        ]);

        $this->setFlash('success', 'Islem Silindi !');
        $this->transactionActionRepository->action(Auth::user(), $transaction, 'transaction-cancelled');

        InformClientJob::dispatch($transaction, 'F', $editTime, $statusId)->onQueue('information_queue');

        return $this->setReturnPage($oldTransaction);
    }

    public function addNote(Request $request): \Illuminate\Http\RedirectResponse
    {
        $transaction = Transaction::with('client', 'method', 'status', 'type', 'transactionable')
            ->find($request->get('transaction_id'));

        Note::create([
            'message' => $request->get('note'),
            'financier_id' => Auth::id(),
            'transaction_id' => $request->get('transaction_id')
        ]);

        $this->setFlash('success', 'Not Ekledi !');

        return $this->setReturnPage($transaction);
    }

    public function letclient(Request $request, $id): \Illuminate\Http\RedirectResponse
    {


        $transaction = Transaction::with('client', 'method', 'status', 'type', 'transactionable', 'account.accountable')
            ->find($id);

        if ($transaction->status->key === 'completed') {
            InformClientJob::dispatch($transaction, 'S', $transaction->edit_time, $transaction->status_id)->onQueue('information_queue');
            $this->setFlash('success', 'Bilgilendirme YAPILDI ! ' . $id);
        } else {
            $this->setFlash('error', 'Bilgilendirme yapilamaz ! ' . $id);
        }

        return Redirect::back();
    }

    public function detail(Request $request, $id)
    {
        $transaction = Transaction::with('client', 'method', 'status', 'type', 'transactionable', 'account.accountable')
            ->find($id);

        $notes = Note::where('transaction_id', $transaction->id)
            ->orderBy('id', 'desc')
            ->get();

        $transaction->notes = $notes;

        if (!$transaction) {
            $this->setFlash('error', 'Islem Bulunamadi !');
            return Redirect::back();
        }

        $fields = array_merge($transaction->transactionable->toArray(), ['amount' => $transaction->amount]);


        return view('financier.transactions.detail')->with([
            'transaction' => $transaction,
            'fields' => $fields,
            'bankInfo' => null,
            'notes' => $transaction->notes,
//            'banka' => $bank ? $bank->name : ''

        ]);
    }

    public function bankInfo(Request $request, $id)
    {
        $transaction = Transaction::with('client', 'method', 'status', 'type', 'transactionable', 'account.accountable')
            ->find($id);

        if (!$transaction) {
            $this->setFlash('error', 'Islem Bulunamadi !');
            return Redirect::back();
        }

        if (!$transaction->account_id) {
            $this->setFlash('error', 'Islem Onaylanmamis gorunmektedir !');
            return Redirect::back();
        }

        $bankInfo = $transaction->account->accountable->toArray();
        $fields = array_merge($transaction->transactionable->toArray(), ['amount' => $transaction->amount]);

        $bank = null;

        if (array_key_exists('bank_id', $bankInfo)) {
            $bank = Bank::find($bankInfo['bank_id']);
            $bankName = $bank ? $bank->name : '';
        } else {
            $bankName = 'PAPARA';
        }

        return view('financier.transactions.detail')->with([
            'transaction' => $transaction,
            'fields' => $fields,
            'bankInfo' => array_merge(['banka' => $bankName], $bankInfo)
        ]);
    }

    public function assign(Request $request, $id)
    {

        $banks = Bank::where('is_active', true)->select('id', 'name', 'key', 'is_active')->get();


        $clientIds = ClientFinancier::where('financier_id', Auth::id())->pluck('client_id')->toArray();
        $transaction = Transaction::with('client', 'method', 'status', 'type', 'transactionable')->find($id);
        $theBank = Bank::where('name', $transaction->transactionable->bank)->first();

        if ($request->filled('bank_id')) {
            $bank = Bank::find($request->get('bank_id'));
            $bankName = $bank->name;
            $owner = $request->get('owner');
            $iban = $request->get('iban');
            $branch = $request->get('branch');
            $accno = $request->get('accno');
        } else {

            $owner = '';
            $iban = '';
            $branch = '';
            $accno = '';
            $bankName = $transaction->transactionable->bank;
        }

        $formFilter = [
            'owner' => $owner,
            'iban' => $iban,
            'branch' => $branch,
            'accno' => $accno,
            'bankName' => $bankName
        ];;

//        $clientAccountAgreement = ClientAccountAgreement::where([
//            'client_id' => $transaction->client_id,
//            'method_id' => $transaction->method_id
//        ])->with('accountType')->first();


        $accounts = Account::whereIn('client_id', $clientIds)
            ->where('is_active', true)
            ->has('accountable')
            // CLIENT EGER KISISEL HESAPLARINI KULLANMAK ISTIYORSA
//            ->when($clientAccountAgreement->accountType->key === 'personal',
//                function ($query) use ($transaction) {
//                    return $query->where(function ($qqq) use ($transaction) {
//                        return $qqq->where('client_id', $transaction->client_id)
//                            ->whereHas('type', function ($typeQuery) {
//                                return $typeQuery->where('key', 'personal');
//                            });
//                    });
//                })
//            // CLIENT EGER SISTEMIN HESAPLARINI KULLANMAK ISTIYORSA
//            ->when($clientAccountAgreement->accountType->key === 'global',
//                function ($query) use ($transaction) {
//                    return $query->where(function ($qqq) {
//                        return $qqq->whereNull('client_id')
//                            ->whereHas('type', function ($typeQuery) {
//                                return $typeQuery->where('key', 'global');
//                            });
//                    });
//                })
//            // CLIENT EGER TUM HESAPLARI KULLANMAK ISTIYORSA
//            ->when($clientAccountAgreement->accountType->key === 'all',
//                function ($query) use ($transaction) {
//                    return $query->where(function ($qqq) use ($transaction) {
//                        return $qqq->whereNull('client_id')
//                            ->orWhere('client_id', $transaction->client_id);
//                    });
//                })
            ->where(function ($qqq) use ($transaction) {
                return $qqq->whereNull('client_id')
                    ->orWhere('client_id', $transaction->client_id);
            })
            //            ->when($transaction->method->key === 'papara',
//                function ($query) use ($transaction) {
//                    return $query->whereHasMorph('accountable', PaparaAccount::class, function ($query) use ($transaction) {
//                        return $query->where('currency_id', $transaction->currency_id);
//                    })->with('accountable', 'accountable.currency');
//                },
//                function ($query) use ($transaction) {
//                    return $query->whereHasMorph('accountable', BankAccount::class, function ($query) use ($transaction) {
//                        return $query->where('currency_id', $transaction->currency_id);
//                    })->with('accountable.bank', 'accountable.currency');
//                })
            ->when($transaction->method->key === 'havale', function ($q) use ($transaction, $formFilter) {
                return $q->whereHasMorph('accountable', BankAccount::class, function ($query) use ($transaction, $formFilter) {
                    return $query->whereHas('bank', function ($qq) use ($formFilter) {
                        return $qq->where('name', $formFilter['bankName']);
                    })->where('currency_id', $transaction->currency_id)->where(function ($qqq) use ($formFilter) {
                        return $qqq
                            ->when($formFilter['owner'], function ($q) use ($formFilter) {
                                return $q->orWhere('owner', 'like', '%' . $formFilter['owner'] . '%');
                            })
                            ->when($formFilter['iban'], function ($q) use ($formFilter) {
                                return $q->orWhere('iban', 'like', '%' . $formFilter['iban'] . '%');
                            })
                            ->when($formFilter['accno'], function ($q) use ($formFilter) {
                                return $q->orWhere('accno', 'like', '%' . $formFilter['accno'] . '%');
                            })
                            ->when($formFilter['branch'], function ($q) use ($formFilter) {
                                return $q->orWhere('branch', 'like', '%' . $formFilter['branch'] . '%');
                            });
                    });
                })
                    ->with('accountable.bank', 'accountable.currency');
            }, function ($q) use ($transaction, $formFilter) {
                return $q->whereHasMorph('accountable', PaparaAccount::class, function ($query) use ($transaction, $formFilter) {
//                    return $query->whereHas('bank', function ($qq) use ($formFilter) {
//                        return $qq->where('name', $formFilter['bankName']);
//                    })->where('currency_id', $transaction->currency_id)->where(function ($qqq) use ($formFilter) {
//                        return $qqq
//                            ->when($formFilter['owner'], function ($q) use ($formFilter) {
//                                return $q->orWhere('owner', 'like', '%' . $formFilter['owner'] . '%');
//                            })
//                            ->when($formFilter['iban'], function ($q) use ($formFilter) {
//                                return $q->orWhere('iban', 'like', '%' . $formFilter['iban'] . '%');
//                            })
//                            ->when($formFilter['accno'], function ($q) use ($formFilter) {
//                                return $q->orWhere('accno', 'like', '%' . $formFilter['accno'] . '%');
//                            })
//                            ->when($formFilter['branch'], function ($q) use ($formFilter) {
//                                return $q->orWhere('branch', 'like', '%' . $formFilter['branch'] . '%');
//                            });
//                    });
                })->with('accountable', 'accountable.currency');
            })
            ->orderBy('created_at')
            ->get()
//            ->paginate(20)
//            ->appends($request->except('page'))
            ->sortBy(function ($item, $key) {
                return $item->accountable->id;
            });


        $fields = array_merge($transaction->transactionable->toArray(), [
            'amount' => $transaction->amount . ' - ' . $transaction->currency_code,
        ]);

        return view('financier.transactions.assign')->with([
            'accounts' => $accounts,
            'transaction' => $transaction,
            'fields' => $fields,
            'banks' => $banks,
            'bankName' => $bankName,
            'theBankId' => $theBank ? $theBank->id : null,
            'formFilter' => $formFilter
        ]);
    }

    public function assignComplete(Request $request, $id): \Illuminate\Http\RedirectResponse
    {

        $transaction = Transaction::with('client', 'method', 'status', 'type', 'transactionable')
            ->find($request->get('transaction_id'));

        if (!$transaction) {
            $this->setFlash('error', 'Islem bulunamadi !');
            return Redirect::back();
        }

        $oldTransaction = $transaction;

        $account = Account::find($id);

        if ($transaction->type->key === 'withdraw') {
            $editTime = $transaction->edit_time;
            $statusId = $transaction->status_id;

            $oldTransaction = $transaction;
            $completedStatus = $this->transactionStatusRepository->getByKey('completed');
            $transaction->update([
                'status_id' => $completedStatus->id,
                'approved_amount' => $request->get('approved_amount'),
                'edit_time' => Carbon::now()
            ]);
            $this->transactionActionRepository->action(Auth::user(), $transaction, $oldTransaction->type->key === 'withdraw' ? 'withdraw-transaction-completed' : 'deposit-transaction-completed');


            InformClientJob::dispatch($transaction, 'S', $editTime, $statusId)->onQueue('information_queue');
        } else {

            $approved = $this->transactionStatusRepository->getByKey($transaction->type->key === 'deposit' ? 'approved' : 'completed');

            $transaction->update([
                'account_id' => $account->id,
                'status_id' => $approved->id
            ]);

            $this->transactionActionRepository->action(Auth::user(), $transaction, 'deposit-bank-info-send');

            $this->setFlash('success', 'Banka Atamasi Yapildi');
        }

        return $this->setReturnPage($oldTransaction);

    }

    public function otoAssign(Request $request, $id): \Illuminate\Http\RedirectResponse
    {
        $clientIds = ClientFinancier::where('financier_id', Auth::id())->pluck('client_id')->toArray();
        $transaction = Transaction::with('client', 'method', 'status', 'type', 'transactionable')->find($id);

        if (!$transaction) {
            $this->setFlash('error', 'Islem bulunamadi !');
            return Redirect::back();
        }

        $clientAccountAgreement = ClientAccountAgreement::where([
            'client_id' => $transaction->client_id,
            'method_id' => $transaction->method_id
        ])->with('accountType')->first();


        $oldTransaction = $transaction;

        $bankAccounts = Account::whereIn('client_id', $clientIds)
            ->where('is_active', true)
            ->when($clientAccountAgreement->accountType->key === 'personal',
                function ($query) use ($transaction) {
                    return $query->where(function ($qqq) use ($transaction) {
                        return $qqq->where('client_id', $transaction->client_id)
                            ->whereHas('type', function ($typeQuery) {
                                return $typeQuery->where('key', 'personal');
                            });
                    });
                })
            ->when($clientAccountAgreement->accountType->key === 'global',
                function ($query) use ($transaction) {
                    return $query->where(function ($qqq) {
                        return $qqq->whereNull('client_id')
                            ->whereHas('type', function ($typeQuery) {
                                return $typeQuery->where('key', 'global');
                            });
                    });
                })
            ->when($clientAccountAgreement->accountType->key === 'all',
                function ($query) use ($transaction) {
                    return $query->where(function ($qqq) use ($transaction) {
                        return $qqq->whereNull('client_id')
                            ->orWhere('client_id', $transaction->client_id);
                    });
                })
            ->when($transaction->method->key === 'papara',
                function ($query) use ($transaction) {
                    return $query->whereHasMorph('accountable', PaparaAccount::class, function ($query) use ($transaction) {
                        return $query->where('currency_id', $transaction->currency_id);
                    })->with('accountable');
                },
                function ($query) use ($transaction) {
                    return $query->whereHasMorph('accountable', BankAccount::class, function ($query) use ($transaction) {
                        return $query->where('currency_id', $transaction->currency_id);
                    })->with('accountable.bank');
                })
            ->get();


        if (!$bankAccounts || count($bankAccounts) <= 0) {
            $this->setFlash('error', 'Uygun Banka Bulunamadi');
            return Redirect::back();
        }

        $account = array_values($bankAccounts->sortBy(function ($product) {
            return $product->todayCount;
        })->toArray())[0];

        $approved = $this->transactionStatusRepository->getByKey('approved');

        $transaction->update([
            'account_id' => $account['id'],
            'status_id' => $approved->id
        ]);

        $this->setFlash('success', 'Banka Atamasi Yapildi');

        $this->transactionActionRepository->action(Auth::user(), $transaction, 'deposit-oto-bank-info-send');

        return $this->setReturnPage($oldTransaction);

    }

    private function setReturnPage($transaction): \Illuminate\Http\RedirectResponse
    {
        if (!$transaction) {
            $this->setFlash('error', 'Islem bulunamadi !');
            return Redirect::back();
        }
        $methodKey = $transaction->method->key;
        $statusKey = $transaction->status->key;
        $typeKey = $transaction->type->key;

        $prefix = 'financier.transactions';

        $route = $prefix . '.' . $methodKey . '.' . $statusKey . '-' . $typeKey . 's';

        return Redirect::route($route);

    }

    private function setFlash($type, $message)
    {
        Session::flash('message', $message);
        Session::flash('type', $type);
    }


}
