@props(['status'])
@php
    // Satu-satunya sumber label & warna status pesanan/transaksi di seluruh aplikasi.
    $map = [
        'Pending'   => ['Menunggu pembayaran', 'bg-amber-100 text-amber-800'],
        'Diproses'  => ['Sedang diproses', 'bg-blue-100 text-blue-800'],
        'Dikirim'   => ['Dalam pengiriman', 'bg-indigo-100 text-indigo-800'],
        'Completed' => ['Selesai', 'bg-emerald-100 text-emerald-800'],
        'Cancelled' => ['Dibatalkan', 'bg-red-100 text-red-700'],
    ];
    [$label, $class] = $map[$status] ?? [$status, 'bg-gray-100 text-gray-700'];
@endphp
<span {{ $attributes->class(['inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold whitespace-nowrap', $class]) }}>{{ $label }}</span>
