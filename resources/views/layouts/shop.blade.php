<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SMART BOOKS - Toko Buku Online')</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Flex:opsz,wght@8..144,300..800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Roboto Flex', sans-serif; background-color: #f7f9fb; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        @media print { header, footer, .no-print { display: none !important; } }
    </style>
</head>
@php
    $authUser = Auth::user();
    $isPelanggan = $authUser && $authUser->role === 'pelanggan';
    $cartCount = \App\Support\Cart::count();
@endphp
<body class="text-[#191c1e] min-h-screen flex flex-col antialiased">
    <header class="bg-white border-b border-[#c3c6d7] sticky top-0 z-40">
        <div class="max-w-6xl mx-auto px-4 h-16 flex items-center gap-4">
            <a href="{{ route('landing') }}" class="text-xl font-black text-[#004ac6] shrink-0">SMART BOOKS</a>

            <form action="{{ route('landing') }}" method="GET" class="hidden md:flex flex-1 max-w-md mx-auto">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari judul, penulis, atau ISBN..." class="w-full rounded-full border-gray-300 text-sm px-4 py-2">
            </form>

            <nav class="ml-auto flex items-center gap-1 text-sm font-medium">
                <a href="{{ route('keranjang.index') }}" class="relative flex items-center gap-1.5 px-3 py-2 rounded-full hover:bg-gray-100 {{ request()->routeIs('keranjang.*') ? 'text-[#004ac6]' : 'text-[#434655]' }}">
                    <span class="material-symbols-outlined">shopping_cart</span>
                    <span class="hidden sm:inline">Keranjang</span>
                    @if($cartCount > 0)
                        <span class="absolute -top-0.5 left-6 min-w-[18px] h-[18px] px-1 rounded-full bg-[#ba1a1a] text-white text-[10px] font-bold flex items-center justify-center">{{ $cartCount }}</span>
                    @endif
                </a>

                @if($isPelanggan)
                    <a href="{{ route('pesanan.index') }}" class="flex items-center gap-1.5 px-3 py-2 rounded-full hover:bg-gray-100 {{ request()->routeIs('pesanan.*') ? 'text-[#004ac6]' : 'text-[#434655]' }}">
                        <span class="material-symbols-outlined">receipt_long</span>
                        <span class="hidden sm:inline">Pesanan</span>
                    </a>
                    <a href="{{ route('profile.index') }}" class="flex items-center gap-1.5 px-3 py-2 rounded-full hover:bg-gray-100 {{ request()->routeIs('profile.*') ? 'text-[#004ac6]' : 'text-[#434655]' }}">
                        <span class="material-symbols-outlined">account_circle</span>
                        <span class="hidden sm:inline">{{ \Illuminate\Support\Str::words($authUser->name, 1, '') }}</span>
                    </a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" title="Keluar" class="flex items-center px-3 py-2 rounded-full text-[#ba1a1a] hover:bg-[#ffdad6]">
                            <span class="material-symbols-outlined">logout</span>
                        </button>
                    </form>
                @elseif($authUser)
                    <a href="{{ $authUser->role === 'admin' ? route('admin.dashboard') : ($authUser->role === 'kurir' ? route('kurir.dashboard') : route('kasir.dashboard')) }}" class="px-4 py-2 rounded-full bg-[#004ac6] text-white hover:bg-blue-700">Buka Panel</a>
                @else
                    <a href="{{ route('login') }}" class="px-3 py-2 rounded-full text-[#434655] hover:bg-gray-100">Masuk</a>
                    <a href="{{ route('register') }}" class="px-4 py-2 rounded-full bg-[#004ac6] text-white hover:bg-blue-700">Daftar</a>
                @endif
            </nav>
        </div>
    </header>

    <main class="flex-1 w-full max-w-6xl mx-auto px-4 py-6 md:py-8">
        @foreach(['success' => 'emerald', 'warning' => 'amber', 'error' => 'red'] as $key => $color)
            @if(session($key))
                <div class="mb-5 p-4 rounded-xl bg-{{ $color }}-50 text-{{ $color }}-800 border border-{{ $color }}-200 text-sm">{{ session($key) }}</div>
            @endif
        @endforeach

        @yield('content')
    </main>

    <footer class="bg-white border-t border-[#c3c6d7] py-5 px-4 text-xs text-[#737686]">
        <div class="max-w-6xl mx-auto flex flex-col sm:flex-row justify-between gap-1">
            <span>&copy; {{ date('Y') }} SMART BOOKS</span>
            <span>Toko buku online &bull; Harga belum termasuk PPN dan ongkos kirim</span>
        </div>
    </footer>
</body>
</html>
