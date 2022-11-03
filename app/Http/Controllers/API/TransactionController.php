<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function all(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $status = $request->input('status');

        if ($id) {
            $transaction = Transaction::with(['user.product'])->find($id);

            if ($transaction) {
                return ResponseFormatter::success(
                    $transaction,
                    'Data Transaksi berhasil diambil'
                );
            } else {
                return ResponseFormatter::error(
                    null,
                    'Data Transaksi gagal diambil',
                    404
                );
            }
        }
        $transaction = Transaction::with(['user.product'])->where('transactions_id', Auth::user()->id);

        if ($status) {
            $transaction->where('status', $status);
        }
        ResponseFormatter::success(
            $transaction->where($name),
            'Data List transaksi berhasil diambil'
        );
    }
    public function checkout(Request $request)
    {
        $request->validate(
            [   
                'name' => 'required', 
                'total_price' => 'required',
                'payment' => 'required',
                'status' => 'required|in:PENDING,SUCCESS,CANCELLED,FAILED,SHIPPING,SHIPPED',
            ]);
            $transaction = Transaction::create([
                'users_id' => Auth::user()->id,
                'address' => $request->address,
                'total_price' => $request->total_price,
                'payment' => $request->payment,
                'status' => $request->status,
            ]);

            foreach ($request->user() as $product) {
               TransactionItem::create([
                'users_id' => Auth::user()->id,
                'products_id' => $product['id'],
                'transactions_id' => $transaction->id,
               ]);
            }
            return ResponseFormatter::success($transaction->load('user.product'),
            'Transaction Berhasil'
        );
    }
}
