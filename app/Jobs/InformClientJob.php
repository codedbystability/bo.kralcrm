<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InformClientJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $transaction, $statusKey, $editTime, $statusId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($transactionId, $statusKey, $editTime, $statusId)
    {
        Log::info('INFORM CLIENT JOB - ' . $transactionId);
        $this->transaction = $transactionId;
        $this->statusKey = $statusKey;
        $this->editTime = $editTime;
        $this->statusId = $statusId;
    }

    /**
     * Execute the job.
     *
     * @return false
     */
    public function handle(): bool
    {
        try {
            $response = $this->getClientResponse();

            if ($response && $response->status() === 200) {
                $clientResponse = $response->json();
                $this->setUpdatedTransaction($clientResponse);
                return true;
            } else if ($this->statusKey === 'F' && $response->json()['status'] !== 200) {
                $this->setOldTransaction();
            }
        } catch (\Exception $exception) {
            InformClientJob::dispatch($this->transaction->id, $this->statusKey, $this->editTime, $this->statusId)
                ->onQueue('information_queue')
                ->delay(Carbon::now()->addSeconds(20));

            return false;
        }

        return true;
    }

    private function getClientResponse(): \Illuminate\Http\Client\Response
    {
        return Http::post(env('TRANSACTION_PROVIDER_CALLBACK'), [
            'service' => $this->transaction->type->key,
            'amount' => $this->transaction->approved_amount ? $this->transaction->approved_amount : $this->transaction->amount,
            'trx' => $this->transaction->trx,
            'status' => $this->statusKey,
            'direct_approve' => $this->transaction->direct_approve ? 'exists' : null
        ]);
    }

    private function setOldTransaction()
    {
        $this->transaction->update([
            'status_id' => $this->statusId,
            'edit_time' => $this->editTime
        ]);
    }

    private function setUpdatedTransaction($clientResponse)
    {
        $this->transaction->update([
            'client_informed' => $clientResponse['status'] === 200,
            'client_response' => json_encode($clientResponse)
        ]);
    }

}
