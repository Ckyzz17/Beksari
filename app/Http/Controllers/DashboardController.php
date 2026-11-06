<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Distributor;
use App\Models\Penjualan;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function admin()
    {
        $totalBuku = Buku::count();
        $totalDistributor = Distributor::count();
        $totalUser = User::count();
        $totalStok = Buku::sum('stok');
        
        $penjualanHariIni = Penjualan::whereDate('tanggal', today())->count();
        $pendapatanHariIni = Penjualan::whereDate('tanggal', today())
            ->where('status', 'Completed')
            ->sum('total_bayar');

        $totalPenjualanBulanIni = Penjualan::whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->where('status', 'Completed')
            ->sum('total_bayar');

        $pesananPending = Penjualan::where('tipe', 'online')->where('status', 'Pending')->count();

        $lowStockBooks = Buku::where('stok', '<=', 10)->orderBy('stok', 'asc')->take(5)->get();
        $recentTransactions = Penjualan::with('user')->latest()->take(5)->get();

        return view('dashboard.admin', compact(
            'totalBuku',
            'totalDistributor',
            'totalUser',
            'totalStok',
            'penjualanHariIni',
            'pendapatanHariIni',
            'totalPenjualanBulanIni',
            'pesananPending',
            'lowStockBooks',
            'recentTransactions'
        ));
    }

    public function kasir()
    {
        $userId = Auth::id();

        // Ringkasan shift: transaksi POS selesai milik kasir ini pada hari ini.
        $shift = fn () => Penjualan::where('user_id', $userId)
            ->where('tipe', 'pos')
            ->where('status', 'Completed')
            ->whereDate('tanggal', today());

        $jumlahTransaksi = $shift()->count();
        $pendapatanHariIni = (int) $shift()->sum('total_bayar');
        $rataRataTransaksi = $jumlahTransaksi > 0 ? (int) round($pendapatanHariIni / $jumlahTransaksi) : 0;

        // Rincian per metode bayar; kasir butuh angka Tunai untuk mencocokkan uang di laci.
        $perMetodeRaw = $shift()
            ->selectRaw('metode_pembayaran, COUNT(*) as jumlah, SUM(total_bayar) as total')
            ->groupBy('metode_pembayaran')
            ->get()
            ->keyBy('metode_pembayaran');

        $perMetode = collect(['Tunai', 'Kartu', 'QRIS'])->mapWithKeys(fn ($m) => [
            $m => [
                'jumlah' => (int) ($perMetodeRaw[$m]->jumlah ?? 0),
                'total' => (int) ($perMetodeRaw[$m]->total ?? 0),
            ],
        ]);

        $recentTransactions = Penjualan::where('user_id', $userId)
            ->where('tipe', 'pos')
            ->whereDate('tanggal', today())
            ->latest()
            ->take(6)
            ->get();

        // Antrean kerja: pesanan online yang menunggu diperiksa dan yang belum punya kurir.
        $pesananOnlinePending = Penjualan::where('tipe', 'online')->where('status', 'Pending')->count();
        $pesananMenunggu = Penjualan::where('tipe', 'online')
            ->where('status', 'Pending')
            ->orderBy('tanggal')
            ->take(5)
            ->get();
        $perluKurir = Penjualan::where('tipe', 'online')
            ->where('status', 'Diproses')
            ->whereNull('kurir_id')
            ->count();

        return view('dashboard.kasir', compact(
            'jumlahTransaksi',
            'pendapatanHariIni',
            'rataRataTransaksi',
            'perMetode',
            'recentTransactions',
            'pesananOnlinePending',
            'pesananMenunggu',
            'perluKurir'
        ));
    }
}
