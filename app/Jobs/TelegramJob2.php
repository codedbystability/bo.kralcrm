<?php

namespace App\Jobs;

use App\Services\TelegramService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TelegramJob2 implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $transaction, $message;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($transaction, $message)
    {

        $this->transaction = $transaction;
        $this->message = $message;;
        //$transaction->client->username, $message, $transaction->type->key, $transaction->method->key
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $telegramService = new TelegramService($this->transaction->client->username, $this->message, $this->transaction->type->key, $this->transaction->method->key);
        $telegramService->sendMessage();
    }
}
