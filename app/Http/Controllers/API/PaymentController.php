<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Midtrans\Config;
use Midtrans\Snap;
class PaymentController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    public function createCharge(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        $harga = OrderItem::where('order_id', $request->order_id)->sum('qty * price');
       
        $params = [
            'transaction_details' => [
                'order_id' => $request->order_id,
                'gross_amount' => $harga,
            ],
            'customer_details' => [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'oIg0Z@example.com',
            ],
        ];

        $snapToken = Snap::getSnapToken($params);
        return ResponseHelper::success('Token Pembayaran Berhasil Dibuat', [
            'snap_token' => $snapToken,
        ], 200);
    }
}
