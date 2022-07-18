<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class TelegramService
{

    private $baseUrl = 'https://api.telegram.org/bot';
    private $botKey = '5549577053:AAH7TfY2xZ7QEU2ahosDb-_06X7STtz0SNk/';
//    private $chatId = '-721975645';
    private $chatId; // TEST GROUP
    private $text;

    public function __construct($clientUsername = null, $text = '', $type = 'deposit', $paymentType = 'havale')
    {

        if ($clientUsername === 'ekolclient') {

            //EkolHavaleYatirim - -796009808
            //EkolHavaleCekim - -542903836
            //EkolPaparaYatirim - -551612633
            //EkolPaparaCekim - -627875899


            if ($paymentType === 'havale') {
                $this->chatId = $type === 'deposit' ? '-796009808' : '-542903836';
            } else if ($paymentType === 'papara') {
                //papara
                $this->chatId = $type === 'deposit' ? '-551612633' : '-627875899';
            }

        } else if ($clientUsername === 'kaleclient') {


            //KaleHavaleYatirim - -1001389605902
            //KaleHavaleCekim - -753742906
            //KalePaparaYatirim - -766774090
            //KalePaparaCekim - -605777880

            if ($paymentType === 'havale') {
                $this->chatId = $type === 'deposit' ? '-1001389605902' : '-753742906';
            } else if ($paymentType === 'papara') {
                //papara
                $this->chatId = $type === 'deposit' ? '-766774090' : '-605777880';
            }
        }



//        if ($paymentType === 'havale') {
//            $this->chatId = $type === 'deposit' ? '-1001389605902' : '-753742906';
//        } else {
//            //papara
//            $this->chatId = $type === 'deposit' ? '-551612633' : '-627875899';
//        }

        $date = "<strong>" . Carbon::now('Europe/Istanbul')->format('Y-m-d H:i:s') . "</strong>";

        $this->text = urlencode($text . $date);
    }

    public function sendMessage()
    {
        try {
            return Http::get($this->baseUrl . $this->botKey . 'sendMessage?chat_id=' . $this->chatId . '&text=' . $this->text . '&parse_mode=html')->json();
        } catch (\Exception $exception) {
            return $exception;
        }
    }

//    public function getChatID()
//    {
//        #financeWithdraw = -753742906 // todo kral withdraw
//        #financeDeposit =  -681574634 // todo kral deposit
//
//        #epayWithdraw = -711621222 // todo e-pay withdraw
//        #epayWithdraw = -799153878 // todo e-pay deposit
//    }

}
