<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class TransactionController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validasi data transaksi
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => [
                'required',
                'exists:products,id'
            ],
            'items.*.qty' => [
                'required',
                'integer',
                'min:1'
            ],
        ]);

        // 2. Proses penyimpanan transaksi
        $transaction = DB::transaction(function () use ($validated, $request) {

            $total = 0;
            $details = [];

            foreach ($validated['items'] as $item) {

                $product = Product::findOrFail($item['product_id']);

                $subtotal = $product->price * $item['qty'];

                $total += $subtotal;

                $details[] = [
                    'product_id' => $product->id,
                    'qty' => $item['qty'],
                    'subtotal' => $subtotal,
                ];
            }

            // 3. Simpan transaksi utama
            $transaction = Transaction::create([
                'user_id' => $request->user()->id,
                'total' => $total,
            ]);

            // 4. Simpan detail transaksi
            $transaction->details()->createMany($details);

            return $transaction;
        });

        // 5. Kembalikan response JSON
        return response()->json([
            'message' => 'Transaksi berhasil dibuat',
            'data' => $transaction->load('details.product'),
        ], 201);
    }

    // Menampilkan detail transaksi berdasarkan ID
    public function show(Transaction $transaction)
    {
        // Periksa hak akses pengguna melalui TransactionPolicy
        Gate::authorize('view', $transaction);

        // Ambil data pengguna dan detail produk
        $transaction->load(['user', 'details.product']);

        // Kembalikan data menggunakan TransactionResource
        return new TransactionResource($transaction);
    }
}
