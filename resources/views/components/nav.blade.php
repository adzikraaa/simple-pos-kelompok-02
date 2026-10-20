<nav class="bg-slate-900 text-white px-4 py-3 flex items-center gap-6">
    <span class="font-semibold">Simple POS</span>

    <a href="{{ route('pos.create') }}"
        class="hover:underline {{ request()->routeIs('pos.create') ? 'font-semibold underline' : '' }}">
        Kasir
    </a>

    <a href="{{ route('transactions.index') }}"
        class="hover:underline {{ request()->routeIs('transactions.index', 'transactions.show') ? 'font-semibold underline' : '' }}">
        Transaksi
    </a>

    @if (auth()->user()?->isAdmin())
        <a href="{{ route('products.index') }}"
            class="hover:underline {{ request()->routeIs('products.*') ? 'font-semibold underline' : '' }}">
            Produk
        </a>

        <a href="{{ route('categories.index') }}"
            class="hover:underline {{ request()->routeIs('categories.*') ? 'font-semibold underline' : '' }}">
            Kategori
        </a>
    @endif

    <span class="ml-auto text-sm">
        {{ auth()->user()?->name }} ({{ auth()->user()?->role }})
    </span>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="hover:underline">
            Keluar
        </button>
    </form>
</nav>
