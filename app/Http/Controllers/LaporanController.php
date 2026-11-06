<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Pasok;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    private function range(Request $request): array
    {
        $start = $request->input('start_date') ?: now()->startOfMonth()->format('Y-m-d');
        $end = $request->input('end_date') ?: now()->format('Y-m-d');

        return [$start, $end];
    }

    public function penjualan(Request $request)
    {
        [$startDate, $endDate] = $this->range($request);

        $penjualans = Penjualan::with(['user', 'details.buku'])
            ->whereDate('tanggal', '>=', $startDate)
            ->whereDate('tanggal', '<=', $endDate)
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $totalOmset = Penjualan::whereDate('tanggal', '>=', $startDate)
            ->whereDate('tanggal', '<=', $endDate)
            ->where('status', 'Completed')
            ->sum('total_bayar');

        return view('laporan.penjualan', compact('penjualans', 'startDate', 'endDate', 'totalOmset'));
    }

    public function pasok(Request $request)
    {
        [$startDate, $endDate] = $this->range($request);

        $base = fn () => Pasok::whereDate('tanggal', '>=', $startDate)->whereDate('tanggal', '<=', $endDate);

        $pasoks = $base()->with(['distributor', 'buku'])
            ->orderByDesc('tanggal')->orderByDesc('id')
            ->paginate(15)->withQueryString();

        $totalUnit = $base()->sum('jumlah');
        $totalTransaksi = $base()->count();

        $perDistributor = $base()->with('distributor')
            ->selectRaw('distributor_id, SUM(jumlah) as total')
            ->groupBy('distributor_id')
            ->orderByDesc('total')
            ->get();

        return view('laporan.pasok', compact('pasoks', 'startDate', 'endDate', 'totalUnit', 'totalTransaksi', 'perDistributor'));
    }

    public function inventori()
    {
        $bukus = Buku::orderBy('stok', 'asc')->paginate(20);
        $totalStok = Buku::sum('stok');
        $totalNilaiAset = Buku::selectRaw('SUM(harga_pokok * stok) as total')->value('total');

        return view('laporan.inventori', compact('bukus', 'totalStok', 'totalNilaiAset'));
    }

    public function pendapatan(Request $request)
    {
        [$startDate, $endDate] = $this->range($request);

        $completed = fn () => Penjualan::where('status', 'Completed')
            ->whereDate('tanggal', '>=', $startDate)
            ->whereDate('tanggal', '<=', $endDate);

        $omset = (int) $completed()->sum('total_bayar');
        $ppn = (int) $completed()->sum('ppn');
        $penjualanBersih = (int) $completed()->sum(DB::raw('subtotal - diskon'));
        $jumlahTransaksi = $completed()->count();

        $hpp = (int) DB::table('detail_penjualans as d')
            ->join('penjualans as p', 'p.id', '=', 'd.penjualan_id')
            ->join('bukus as b', 'b.id', '=', 'd.buku_id')
            ->where('p.status', 'Completed')
            ->whereDate('p.tanggal', '>=', $startDate)
            ->whereDate('p.tanggal', '<=', $endDate)
            ->sum(DB::raw('d.jumlah * COALESCE(d.harga_pokok_satuan, b.harga_pokok)'));

        $labaKotor = $penjualanBersih - $hpp;
        $margin = $penjualanBersih > 0 ? round($labaKotor / $penjualanBersih * 100, 1) : 0;

        $harian = $completed()
            ->selectRaw('DATE(tanggal) as hari, COUNT(*) as trx, SUM(subtotal - diskon) as bersih, SUM(total_bayar) as omset')
            ->groupBy('hari')
            ->orderBy('hari', 'desc')
            ->get();

        return view('laporan.pendapatan', compact(
            'startDate', 'endDate', 'omset', 'ppn', 'penjualanBersih', 'hpp',
            'labaKotor', 'margin', 'jumlahTransaksi', 'harian'
        ));
    }
}
