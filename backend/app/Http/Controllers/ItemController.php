<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ItemController extends Controller
{
    public function index()
    {
        return response()->json(Item::all(), 200);
    }

    public function store(Request $request)
    {
        // Normalisasi data input agar mendukung atribut nama/name, stok/stock, harga/price
        $input = [
            'name'  => $request->input('name') ?? $request->input('nama'),
            'sku'   => $request->input('sku'),
            'stock' => $request->input('stock') ?? $request->input('stok'),
            'price' => $request->input('price') ?? $request->input('harga'),
        ];

        // 1. Validasi Input
        $validator = Validator::make($input, [
            'name'  => 'required|string|max:255',
            'sku'   => 'required|string|unique:items,sku',
            'stock' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
        ]);

        // Log Terstruktur (WARNING): Validasi Input Gagal
        if ($validator->fails()) {
            Log::warning('Gagal menambahkan barang: Validasi input tidak memenuhi syarat', [
                'user'   => $request->user()?->email ?? 'guest_user',
                'action' => 'CREATE_ITEM_VALIDATION_FAILED',
                'reason' => $validator->errors()->toArray(),
                'input'  => $input,
            ]);

            return response()->json([
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            // 2. Simpan Data Barang
            $item = Item::create($validator->validated());

            // Log Terstruktur (INFO): Penyimpanan Data Berhasil
            Log::info('Berhasil menambahkan data barang baru ke inventaris', [
                'user'    => $request->user()?->email ?? 'authenticated_user',
                'action'  => 'CREATE_ITEM_SUCCESS',
                'item_id' => $item->id,
                'sku'     => $item->sku,
            ]);

            return response()->json($item, 201);
        } catch (\Throwable $e) {
            // Log Terstruktur (ERROR): Gagal Menyimpan ke Basis Data
            Log::error('Gagal menyimpan barang ke basis data akibat kesalahan sistem', [
                'user'   => $request->user()?->email ?? 'authenticated_user',
                'action' => 'CREATE_ITEM_DATABASE_ERROR',
                'reason' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Terjadi kesalahan pada basis data',
            ], 500);
        }
    }
}