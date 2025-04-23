<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $order = Order::with('orderItems')->get();
        return ResponseHelper::success('List Data Order', $order, 200);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $data = Order::with('table', 'orderItems')->find($id);
        if (!$data) {
            return ResponseHelper::error('Data not found', 404);
        }
        return ResponseHelper::success('Data found', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function createOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'table_number' => 'required|exists:tables,table_number',
            'customer_name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::error($validator->errors()->first(), 422);
        }

        // Buat order baru
        $order = Order::create([
            'table_number' => $request->table_number,
            'customer_name' => $request->customer_name,
            'status' => 'pending', // Status awal, menunggu pembayaran
        ]);

        return ResponseHelper::success('Order berhasil dibuat', $order, 201);
    }
    public function showByTable($table_number)
    {
        // Cari order dengan status pending berdasarkan nomor meja
        $order = Order::where('table_number', $table_number)
            ->where('status', 'pending')
            ->with(['items.menu']) // Load data menu dalam order
            ->first();

        // Jika order tidak ditemukan, buat order baru
        if (!$order) {
            return ResponseHelper::error('Belum Terdapat Pesanan', 404);
        }

        // Hitung total harga order
        $total_price = $order->items->sum(function ($item) {
            return $item->menu->price * $item->quantity;
        });

        return response()->json([
            'message' => 'Order ditemukan',
            'order' => $order,
            'total_price' => $total_price,
        ], 200);
    }
    public function buatPesanan(Request $request)
    {
        // 1. Validasi data
        $validated = Validator::make($request->all(), [
             'customer_name' => 'required|string',
            'table_id' => 'required|exists:tables,id',
            'order_items' => 'required|array|min:1',
            'order_items.*.menu_id' => 'required|exists:menus,id',
            'order_items.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validated->fails()) {
            return ResponseHelper::error($validated->errors()->first(), 422);
        }

        // 2. Buat order baru
        $order = Order::create([
            'customer_name' => $validated['customer_name'],
            'table_id' => $validated['table_id'],
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        // 3. Loop untuk simpan item ke order_items
        foreach ($validated['order_items'] as $item) {
            $menu = Menu::findOrFail($item['menu_id']);

            OrderItem::create([
                'order_id' => $order->id,
                'menu_id' => $menu->id,
                'qty' => $item['quantity'],
                'price' => $menu->price, // ambil harga saat ini
            ]);
        }

        return ResponseHelper::success('Order berhasil dibuat', $order, 201);
    }


}
