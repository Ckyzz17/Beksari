<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SMART BOOKS - Sistem Manajemen Toko')</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Flex:opsz,wght@8..144,300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Roboto Flex', sans-serif; background-color: #f7f9fb; color: #191c1e; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        .material-symbols-outlined.fill, .filled-icon { font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24 !important; }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        @media print { aside, header, footer, #mobile-nav, .no-print { display: none !important; } }
    </style>
</head>
@php $authUser = Auth::user(); @endphp
<body class="bg-[#f7f9fb] text-[#191c1e] min-h-screen flex flex-col md:flex-row antialiased overflow-x-hidden">
    <!-- Sidebar desktop -->
    <aside class="hidden md:flex flex-col h-screen w-[280px] sticky left-0 top-0 bg-[#f2f4f6] border-r border-[#c3c6d7] p-4 z-30 shrink-0">
        <div class="mb-6 px-3 pt-2">
            <h1 class="text-2xl font-black text-[#004ac6] tracking-tight">SMART BOOKS</h1>
            <p class="text-[11px] font-medium text-[#434655] mt-1">Sistem Manajemen Toko</p>
        </div>
        @include('layouts._nav')
    </aside>

    <!-- Drawer menu untuk HP -->
    <div id="mobile-nav" class="hidden md:hidden fixed inset-0 z-50">
        <div class="absolute inset-0 bg-black/40" data-close-nav></div>
        <aside class="absolute left-0 top-0 h-full w-[280px] max-w-[85%] bg-[#f2f4f6] p-4 flex flex-col shadow-xl">
            <div class="mb-6 px-3 pt-2 flex items-start justify-between">
                <div>
                    <h1 class="text-2xl font-black text-[#004ac6] tracking-tight">SMART BOOKS</h1>
                    <p class="text-[11px] font-medium text-[#434655] mt-1">Sistem Manajemen Toko</p>
                </div>
                <button type="button" data-close-nav class="p-1 rounded-full text-[#434655] hover:bg-[#e0e3e5]" aria-label="Tutup menu">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            @include('layouts._nav')
        </aside>
    </div>

    <!-- Konten utama -->
    <main class="flex-1 flex flex-col min-h-screen min-w-0">
        <header class="h-16 px-4 md:px-8 bg-white border-b border-[#c3c6d7] flex justify-between items-center sticky top-0 z-20">
            <div class="flex items-center gap-3 flex-1 min-w-0">
                <button type="button" id="open-nav" class="md:hidden p-2 -ml-2 rounded-full text-[#434655] hover:bg-[#e0e3e5]" aria-label="Buka menu">
                    <span class="material-symbols-outlined">menu</span>
                </button>
                <span class="text-base font-bold text-[#004ac6] truncate">@yield('header_title', 'SMART BOOKS')</span>
            </div>

            <div class="flex items-center gap-3">
                <div class="text-right hidden sm:block">
                    <p class="text-xs font-bold text-[#191c1e]">{{ $authUser->name }}</p>
                    <p class="text-[11px] text-[#737686] font-semibold capitalize">{{ $authUser->role }}</p>
                </div>
                @if($authUser->avatar_url)
                    <img src="{{ $authUser->avatar_url }}" class="w-9 h-9 rounded-full object-cover border border-[#c3c6d7]" alt="Foto {{ $authUser->name }}">
                @else
                    <span class="w-9 h-9 rounded-full bg-[#004ac6] text-white flex items-center justify-center text-sm font-bold">{{ strtoupper(mb_substr($authUser->name, 0, 1)) }}</span>
                @endif
            </div>
        </header>

        <div class="p-4 md:p-8 flex-1">
            @php
                $flashes = [
                    'success' => ['check_circle', 'bg-emerald-50 text-emerald-800 border-emerald-200', 'text-emerald-600'],
                    'error'   => ['error', 'bg-rose-50 text-rose-800 border-rose-200', 'text-rose-600'],
                    'warning' => ['warning', 'bg-amber-50 text-amber-800 border-amber-200', 'text-amber-600'],
                    'info'    => ['info', 'bg-blue-50 text-blue-800 border-blue-200', 'text-blue-600'],
                ];
            @endphp
            @foreach($flashes as $key => [$icon, $box, $iconColor])
                @if(session($key))
                    <div class="mb-6 p-4 rounded-2xl border flex items-center gap-2 text-xs font-semibold {{ $box }}">
                        <span class="material-symbols-outlined {{ $iconColor }}">{{ $icon }}</span>
                        <span>{{ session($key) }}</span>
                    </div>
                @endif
            @endforeach

            @yield('content')
        </div>

        <footer class="bg-white py-4 px-4 md:px-8 border-t border-[#c3c6d7] text-xs text-[#737686]">
            &copy; {{ date('Y') }} SMART BOOKS
        </footer>
    </main>

    <script>
        (() => {
            const drawer = document.getElementById('mobile-nav');
            const toggle = (open) => {
                drawer.classList.toggle('hidden', !open);
                document.body.classList.toggle('overflow-hidden', open);
            };
            document.getElementById('open-nav').addEventListener('click', () => toggle(true));
            drawer.querySelectorAll('[data-close-nav]').forEach((el) => el.addEventListener('click', () => toggle(false)));
            document.addEventListener('keydown', (e) => { if (e.key === 'Escape') toggle(false); });
        })();
    </script>
</body>
</html>
