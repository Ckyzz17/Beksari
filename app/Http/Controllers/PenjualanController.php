<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Penjualan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PenjualanController extends Controller
{
    public function index(Request $request)
    {
        $query = Penjualan::with(['user', 'details.buku']);

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where('kode_transaksi', 'like', "%{$search}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $penjualans = $query->latest()->paginate(15)->withQueryString();
        return view('penjualan.index', compact('penjualans'));
    }

    public function show(Penjualan $penjualan)
    {
        $penjualan->load(['user', 'details.buku']);
        return view('penjualan.show', compact('penjualan'));
    }

    /** Daftar pesanan online untuk kasir dan admin. */
    public function onlineIndex(Request $request)
    {
        $query = Penjualan::with(['user', 'kurir', 'details.buku'])->where('tipe', 'online');

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where('kode_transaksi', 'like', "%{$search}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $penjualans = $query->latest()->paginate(15)->withQueryString();

        return view('pesanan_masuk.index', compact('penjualans'));
    }

    /** Detail pesanan online; transaksi POS tidak dapat dibuka lewat halaman ini. */
    public function onlineShow(Penjualan $penjualan)
    {
        abort_unless($penjualan->tipe === 'online', 404);

        $penjualan->load(['user', 'kurir', 'details.buku']);
        $kurirs = User::where('role', 'kurir')->orderBy('name')->get(['id', 'name', 'telepon']);

        return view('penjualan.show', compact('penjualan', 'kurirs'));
    }

    /** Tampilkan bukti pembayaran kepada kasir/admin yang sudah login. */
    public function onlinePaymentProof(Penjualan $penjualan)
    {
        abort_unless($penjualan->tipe === 'online', 404);
        abort_unless($penjualan->payment_proof_path, 404);
        abort_unless(Storage::disk('local')->exists($penjualan->payment_proof_path), 404);

        return response()->file(Storage::disk('local')->path($penjualan->payment_proof_path), [
            'Cache-Control' => 'private, no-store, max-age=0',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /** Tetapkan atau alihkan kurir untuk pesanan yang sudah siap diproses. */
    public function assignCourier(Request $request, Penjualan $penjualan)
    {
        abort_unless($penjualan->tipe === 'online', 404);

        $data = $request->validate([
            'kurir_id' => 'required|integer|exists:users,id',
        ]);

        $courier = User::whereKey($data['kurir_id'])->where('role', 'kurir')->first();
        if (! $courier) {
            return back()->with('error', 'Akun yang dipilih bukan akun kurir.');
        }

        $result = DB::transaction(function () use ($penjualan, $courier) {
            $current = Penjualan::query()->lockForUpdate()->findOrFail($penjualan->id);

            if ($current->status !== 'Diproses') {
                return false;
            }

            $current->update([
                'kurir_id' => $courier->id,
                'kurir_assigned_at' => now(),
            ]);

            return true;
        });

        if (! $result) {
            return back()->with('error', 'Kurir hanya dapat ditetapkan saat pesanan berstatus Diproses.');
        }

        return back()->with('success', 'Kurir berhasil ditetapkan untuk pesanan ini.');
    }

    /** Perbarui status pesanan online dari panel kasir/admin. */
    public function updateOnlineStatus(Request $request, Penjualan $penjualan)
    {
        abort_unless($penjualan->tipe === 'online', 404);

        return $this->updateStatus($request, $penjualan);
    }

    public function updateStatus(Request $request, Penjualan $penjualan)
    {
        $data = $request->validate([
            'status' => 'required|in:Diproses,Dikirim,Completed,Cancelled',
            'nomor_resi' => 'nullable|string|max:100',
        ]);

        $result = DB::transaction(function () use ($penjualan, $data) {
            // Always read the latest status while holding a row lock. This prevents
            // two staff members from advancing/cancelling the same order at once.
            $current = Penjualan::query()->lockForUpdate()->findOrFail($penjualan->id);

            if ($current->tipe === 'online' && in_array($data['status'], ['Dikirim', 'Completed'], true)) {
                return ['type' => 'error', 'message' => 'Status pengiriman dan penyelesaian pesanan online hanya dapat diperbarui oleh kurir yang ditugaskan.'];
            }

            $allowed = [
                'Pending' => ['Diproses', 'Cancelled'],
                'Diproses' => ['Dikirim'],
                'Dikirim' => ['Completed'],
            ];

            if (! in_array($data['status'], $allowed[$current->status] ?? [], true)) {
                return ['type' => 'error', 'message' => 'Perubahan status tidak sesuai alur pesanan atau pesanan sudah diperbarui.'];
            }

            if ($current->kurir_id && in_array($data['status'], ['Dikirim', 'Completed'], true)) {
                return ['type' => 'error', 'message' => 'Pesanan sudah ditugaskan ke kurir. Perubahan status pengiriman dilakukan dari dashboard kurir.'];
            }

            // Pembayaran non-COD wajib memiliki file bukti sebelum kasir dapat memprosesnya.
            if ($current->status === 'Pending' && $data['status'] === 'Diproses'
                && $current->metode_pembayaran !== 'COD'
                && (! $current->payment_proof_path || ! Storage::disk('local')->exists($current->payment_proof_path))) {
                return ['type' => 'error', 'message' => 'Belum ada bukti pembayaran. Minta pelanggan mengunggah bukti dan periksa terlebih dahulu.'];
            }

            if ($data['status'] === 'Cancelled') {
                foreach ($current->details()->get() as $detail) {
                    Buku::whereKey($detail->buku_id)->increment('stok', $detail->jumlah);
                }

                $current->update(['status' => 'Cancelled']);

                return ['type' => 'success', 'message' => 'Pesanan dibatalkan dan stok dikembalikan.'];
            }

            $update = ['status' => $data['status']];

            if ($data['status'] === 'Dikirim') {
                $update['nomor_resi'] = $data['nomor_resi'] ?? null;
                $update['dikirim_pada'] = now();
            }

            if ($data['status'] === 'Completed') {
                $update['diterima_pada'] = now();
                $update['bayar'] = $current->total_bayar;
                $update['kembali'] = 0;
            }

            $current->update($update);

            return ['type' => 'success', 'message' => 'Status pesanan berhasil diperbarui.'];
        });

        return back()->with($result['type'], $result['message']);
    }

}
