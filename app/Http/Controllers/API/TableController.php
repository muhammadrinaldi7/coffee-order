<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Table;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TableController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return ResponseHelper::success('Success', Table::all(), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
        'table_number' => 'required|unique:tables',
    ]);

    if ($validator->fails()) {
        return ResponseHelper::error($validator->errors()->first(), 422);
    }

    // URL untuk order berdasarkan meja
    $url = 'https://localhost:3000/order/' . $request->table_number;

    // Generate QR Code dalam bentuk PNG
    $qrCode = QrCode::format('png')->size(300)->errorCorrection('H')->generate($url);

    // Path untuk menyimpan QR Code
    $fileName = "table_{$request->table_number}.png";
    $filePath = "qrcodes/$fileName";  // Path dalam storage/public/qrcodes

    // Pastikan direktori ada sebelum menyimpan
    Storage::disk('public')->put($filePath, $qrCode);

    // Simpan ke database
    $data = Table::create([
        'table_number' => $request->table_number,
        'qr_code_url' => asset("storage/$filePath"), // URL yang bisa diakses langsung
    ]);

    return ResponseHelper::success('No Meja Berhasil Ditambahkan', $data, 201);
    
    }

     public function printQrCode($id)
    {
        // Ambil data meja berdasarkan ID
        $table = Table::findOrFail($id);

        // Buat QR Code berdasarkan URL pemesanan
        // $url = url("/order?table=" . $table->table_number);
        $url = 'https://localhost:3000/order/' . $table->table_number;
        $qrCode = QrCode::format('png')->size(300)->generate($url);

        // Simpan QR Code ke dalam file sementara
        $filePath = storage_path("app/public/qrcodes/table_$table->table_number.png");
        file_put_contents($filePath, $qrCode);

        // Buat PDF untuk cetak QR Code
        $pdf = Pdf::loadView('pdf.qr_code', compact('table', 'filePath'));

        // Download PDF
        return $pdf->download("QR_Code_Meja_$table->table_number.pdf");
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $table = Table::find($id);

        if (!$table) {
            return ResponseHelper::error('Table not found', 404);
        }
        return ResponseHelper::success('Table found', $table);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'table_number' => 'integer|unique:tables',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::error($validator->errors()->first(), 422);
        }
        $table = Table::find($id);

        if (!$table) {
            return ResponseHelper::error('Table not found', 404);
        }

          // URL untuk order berdasarkan meja
        $url = 'https://localhost:3000/order/' . $request->table_number;

        // Generate QR Code dalam bentuk PNG
        $qrCode = QrCode::format('png')->size(300)->errorCorrection('H')->generate($url);

        // Path untuk menyimpan QR Code
        $fileName = "table_{$request->table_number}.png";
        $filePath = "qrcodes/$fileName";  // Path dalam storage/public/qrcodes

        // Pastikan direktori ada sebelum menyimpan
        Storage::disk('public')->put($filePath, $qrCode);
        $table->update([
             'table_number' => $request->table_number,
             'qr_code_url' => asset("storage/$filePath"), // URL yang bisa diakses langsung
        ]);
        return ResponseHelper::success('Table updated successfully', $table);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $table = Table::find($id);

        if (!$table) {
            return ResponseHelper::error('Table not found', 404);
        }

        $table->delete();
        return ResponseHelper::success('Table deleted successfully');
    }
}
