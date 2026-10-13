<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller
{
    public function create()
    {
        $products = Product::where('stock', '>', 0)->paginate(12);

        return view('pos.create', ['products' => $products]);
    }

    public function store(StoreTransactionRequest $request)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated) {
            $transaction = Transaction::create([
                'user_id' => 1, // Hardcode sementara sampai fitur login tersedia
                'total' => 0,
            ]);

            $total = 0;

            foreach ($validated['items'] as $item) {
                $product = Product::whereKey($item['product_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                // Validasi agar jumlah pembelian tidak melebihi stok
                if ($item['qty'] > $product->stock) {
                    throw ValidationException::withMessages([
                        'items' => "Stok {$product->name} tidak mencukupi. "
                            . "Stok tersedia: {$product->stock}.",
                    ]);
                }

                // Menghitung subtotal berdasarkan harga dari database
                $subtotal = $product->price * $item['qty'];
                $total += $subtotal;

                // Menyimpan detail transaksi
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $product->id,
                    'qty' => $item['qty'],
                    'subtotal' => $subtotal,
                ]);
            }

            // Menyimpan total transaksi
            $transaction->update([
                'total' => $total,
            ]);
        });

        return redirect()
            ->route('pos.create')
            ->with('success', 'Transaksi berhasil disimpan.');
    }

    public function index()
    {
        $transactions = Transaction::with('details.product', 'user')
            ->latest()
            ->paginate(15);

        return view('transactions.index', compact('transactions'));
    }

    public function show(string $id)
    {
        return "Detail transaksi #{$id}";
    }
}
