@extends('layouts.app')

@section('title', 'Kasir')

@section('content')
    <h1 class="text-lg font-semibold mb-4">Transaksi Kasir</h1>

    @if (session('success'))
        <div class="bg-green-50 text-green-700 p-3 rounded-md mb-4">
            {{ session('success') }}
        </div>
    @endif

    @error('items')
        <div class="bg-red-50 text-red-700 p-3 rounded-md mb-4">{{ $message }}</div>
    @enderror

    <form method="POST" action="{{ route('transactions.store') }}" x-data="{
        cart: [],
        addToCart(id, name, price) {
            const existing = this.cart.find(item => item.id === id);
            if (existing) {
                existing.qty++;
            } else {
                this.cart.push({ id, name, price, qty: 1 });
            }
        },
        subtotal() {
            return this.cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
        }
    }">
        @csrf

        <div class="grid grid-cols-3 gap-4">
            @foreach ($products as $product)
                <div class="border rounded-md p-3 cursor-pointer hover:bg-slate-50 transition"
                     @click="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }})">
                    <p class="font-medium">{{ $product->name }}</p>
                    <p class="text-sm text-slate-500">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                </div>
            @endforeach
        </div>

        <div class="mt-6 border-t pt-4">
            <h2 class="font-medium mb-3">Keranjang Belanja</h2>

            <template x-for="(item, index) in cart" :key="item.id">
                <div class="flex items-center justify-between py-2 border-b">
                    <div>
                        <p x-text="item.name + ' x ' + item.qty + ' - Rp ' + (item.price * item.qty).toLocaleString('id-ID')"></p>
                        <input type="hidden" :name="'items[' + index + '][product_id]'" :value="item.id">
                        <input type="hidden" :name="'items[' + index + '][qty]'" :value="item.qty">
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="number" min="1" x-model.number="item.qty">
                    </div>
                </div>
            </template>

            <p class="font-semibold mt-3">Subtotal: Rp <span x-text="subtotal().toLocaleString('id-ID')"></span></p>
            <button type="submit" class="mt-3 bg-blue-600 text-white px-4 py-2 rounded-md">Bayar</button>
        </div>
    </form>
@endsection