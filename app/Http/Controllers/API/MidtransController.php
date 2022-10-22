<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Notification;

class MidtransController extends Controller
{
    public function callback()
    {
        //Set Configurasi Midtrans
        Config::$serverKey = config('services.midtrans.serverKey');
        Config::$isProduction = config('services.midtrans.isProduction');
        Config::$isSanitized = config('services.midtrans.isSanitized');
        Config::$is3ds = config('services.midtrans.is3ds');
        //buat instance midtrans notifikasi
        $notification = new Notification();

        //Assign ke variable untuk memudah coding
        $status = $notification->transaction_status;
        $type = $notification->payment_type;
        $fraud = $notification->fraud_status;
        $order_id = $notification->order_id;

        //Get transaction id
        $order = explode('-', $order_id);

        // cari transaction berdasarkan id
        $transaction = Transaction::findOrFail($order[1]);

        //handle notifikasi status midtrans
        if ($status == 'capture') {
            if ($type == 'credit_card') {
                if ($fraud == 'challenge') {
                    $transaction->status = 'PENDING';
                } else {
                    $transaction->status = 'SUCCESS';
                }
            }
        } else if ($status == 'settlement')
         {
            $transaction->status = 'SUCCESS';
        } else if ($status == 'pending')
         {
            $transaction->status = 'PENDING';
        } else if ($status == 'deny') 
        {
            $transaction->status = 'PENDING';
        } else if ($status == 'expire') 
        {
            $transaction->status = 'CANCELLED';
        } else if ($status == 'cancel') 
        {
            $transaction->status = 'CANCELLED';
        }

        //simpan transaksi
        $transaction->save();

        //return response untuk midtrans
        return response()->json([
            'meta'=>[
                'code' => 200,
                'message' => 'Midtrans Notifikasi Success!'
            ]
        ]);
    }
}
