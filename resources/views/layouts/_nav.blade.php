@php
    $role = Auth::user()->role;

    $groups = [];

    $groups[] = [
        'label' => null,
        'items' => [[
            'route'  => match ($role) { 'admin' => 'admin.dashboard', 'kurir' => 'kurir.dashboard', default => 'kasir.dashboard' },
            'active' => ['admin.dashboard', 'kasir.dashboard', 'kurir.*'],
            'icon'   => $role === 'kurir' ? 'local_shipping' : 'dashboard',
            'label'  => $role === 'kurir' ? 'Pengantaran Saya' : 'Dashboard',
        ]],
    ];

    if ($role === 'admin') {
        $groups[0]['items'] = array_merge($groups[0]['items'], [
            ['route' => 'buku.index',        'active' => ['buku.*'],        'icon' => 'menu_book',     'label' => 'Data Buku'],
            ['route' => 'distributor.index', 'active' => ['distributor.*'], 'icon' => 'local_shipping', 'label' => 'Data Distributor'],
            ['route' => 'user.index',        'active' => ['user.*'],        'icon' => 'group',         'label' => 'Data User'],
            ['route' => 'pasok.index',       'active' => ['pasok.*'],       'icon' => 'inventory',     'label' => 'Pasok Buku'],
            ['route' => 'penjualan.index',   'active' => ['penjualan.*'],   'icon' => 'receipt_long',  'label' => 'Penjualan'],
        ]);
    }

    if (in_array($role, ['admin', 'kasir'], true)) {
        $groups[0]['items'] = array_merge($groups[0]['items'], [
            ['route' => 'pesanan-masuk.index', 'active' => ['pesanan-masuk.*'], 'icon' => 'shopping_bag',   'label' => 'Pesanan Online'],
            ['route' => 'pos.index',           'active' => ['pos.*'],           'icon' => 'point_of_sale', 'label' => 'POS Kasir', 'badge' => 'Langsung'],
        ]);
    }

    if ($role === 'admin') {
        $groups[] = [
            'label' => 'Laporan',
            'items' => [
                ['route' => 'laporan.penjualan',  'active' => ['laporan.penjualan'],  'icon' => 'analytics',       'label' => 'Laporan Penjualan'],
                ['route' => 'laporan.pasok',      'active' => ['laporan.pasok'],      'icon' => 'description',     'label' => 'Laporan Pasok'],
                ['route' => 'laporan.inventori',  'active' => ['laporan.inventori'],  'icon' => 'summarize',       'label' => 'Laporan Inventori'],
                ['route' => 'laporan.pendapatan', 'active' => ['laporan.pendapatan'], 'icon' => 'monetization_on', 'label' => 'Laporan Pendapatan'],
            ],
        ];
    }

    $account = [['route' => 'profile.index', 'active' => ['profile.*'], 'icon' => 'account_circle', 'label' => 'Profil']];
    if ($role === 'admin') {
        $account[] = ['route' => 'setting.index', 'active' => ['setting.*'], 'icon' => 'settings', 'label' => 'Pengaturan Sistem'];
    }
    $groups[] = ['label' => null, 'items' => $account];
@endphp

<nav class="flex-1 overflow-y-auto hide-scrollbar space-y-1 pr-1 text-sm font-medium">
    @foreach($groups as $group)
        @if(! $loop->first)
            <div class="h-px bg-[#c3c6d7] my-3 mx-2 opacity-60"></div>
        @endif
        @if($group['label'])
            <p class="px-4 py-1 text-xs font-bold text-[#737686]">{{ $group['label'] }}</p>
        @endif
        @foreach($group['items'] as $item)
            @php $active = request()->routeIs(...$item['active']); @endphp
            <a href="{{ route($item['route']) }}"
               class="flex items-center gap-3 px-4 py-2.5 rounded-full transition-all {{ $active ? 'bg-[#2563eb] text-white font-bold shadow-sm' : 'text-[#434655] hover:bg-[#e0e3e5]' }}">
                <span class="material-symbols-outlined {{ $active ? 'fill' : '' }}">{{ $item['icon'] }}</span>
                <span class="flex-1">{{ $item['label'] }}</span>
                @isset($item['badge'])
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $active ? 'bg-white text-[#2563eb]' : 'bg-blue-100 text-[#004ac6]' }}">{{ $item['badge'] }}</span>
                @endisset
            </a>
        @endforeach
    @endforeach
</nav>

<div class="mt-auto pt-3 border-t border-[#c3c6d7]">
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-[#ba1a1a] hover:bg-[#ffdad6] rounded-full transition-colors text-sm font-semibold">
            <span class="material-symbols-outlined">logout</span>
            <span>Keluar</span>
        </button>
    </form>
</div>
