
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Tambah Produk</h1>

    <form action="{{ route('products.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="name">Nama Produk</label>
            <input
                type="text"
                name="name"
                id="name"
                class="form-control"
                value="{{ old('name') }}"
            >
            @error('name')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="category_id">Kategori</label>
            <select name="category_id" id="category_id" class="form-control">
                <option value="">-- Pilih Kategori --</option>
                @foreach ($categories as $category)
                    <option
                        value="{{ $category->id }}"
                        {{ old('category_id') == $category->id ? 'selected' : '' }}
                    >
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="price">Harga</label>
            <input
                type="number"
                name="price"
                id="price"
                class="form-control"
                min="0"
                value="{{ old('price') }}"
            >
            @error('price')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="stock">Stok</label>
            <input
                type="number"
                name="stock"
                id="stock"
                class="form-control"
                min="0"
                value="{{ old('stock') }}"
            >
            @error('stock')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">
            Simpan Produk
        </button>

        <a href="{{ route('products.index') }}" class="btn btn-secondary">
            Kembali
        </a>
    </form>
</div>
@endsection