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
        let existingItem = this.cart.find(item => item.id === id);
        if (existingItem) {
            existingItem.qty++;
        } else {
            this.cart.push({ id, name, price, qty: 1 });
        }
    },
    removeItem(index) {
        this.cart.splice(index, 1);
    },
    subtotal() {
        return this.cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
    }
}">
    @csrf

    <div class="grid grid-cols-3 gap-4">
        @foreach ($products as$product)
            <div class="border rounded-md p-3 cursor-pointer hover:bg-slate-50 transition"
                @click="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', {{$product->price }})">
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
                    <p class="font-medium" x-text="item.name"></p>
                    <p class="text-xs text-slate-500" x-text="'Rp ' + item.price.toLocaleString('id-ID')"></p>
                </div>

                <div class="flex items-center gap-2">
                    <input type="number" min="1" x-model.number="item.qty"