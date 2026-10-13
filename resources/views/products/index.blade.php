@extends('layouts.app')

@section('title', 'Produk')

@section('content')

<h1 class="text-lg font-semibold mb-4">Daftar Produk</h1>

<table class="w-full text-left border-collapse">
    <thead>
        <tr class="border-b">
            <th class="py-2 pr-4">Nama</th>
            <th class="py-2 pr-4">Kategori</th>
            <th class="py-2 pr-4">Harga</th>
            <th class="py-2 pr-4">Stok</th>
            <th class="py-2 pr-4">Aksi</th> 
        </tr>
    </thead>

    <tbody>
        @foreach ($products as $product)
            <tr class="border-b">
                <td class="py-2 pr-4">{{ $product->name }}</td>
                <td class="py-2 pr-4">{{ $product->category->name }}</td>
                <td class="py-2 pr-4">Rp {{ number_format($product->price) }}</td>
                <td class="py-2 pr-4">{{ $product->stock }}</td>
                <td class="py-2 pr-4">
                    <a href="{{ route('products.edit', $product->id) }}" class="text-blue-600 hover:underline">Edit</a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

@endsection