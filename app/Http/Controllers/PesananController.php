<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PesananController extends Controller
{
    public function index()
    {
        $pesanans = Penjualan::with('details.buku')
            ->where('user_id', Auth::id())
            ->where('tipe', 'online')
            ->latest()
            ->paginate(10);

        return view('pesanan.index', compact('pesanans'));
    }

    public function show(Penjualan $penjualan)
    {
        $this->authorizeOwner($penjualan);
        $penjualan->load('details.buku');

        return view('pesanan.show', ['pesanan' => $penjualan]);
    }

    /** Simpan atau ganti bukti pembayaran selama pesanan masih menunggu pembayaran. */
    public function uploadPaymentProof(Request $request, Penjualan $penjualan)
    {
        $this->authorizeOwner($penjualan);

        if ($penjualan->status !== 'Pending' || ! in_array($penjualan->metode_pembayaran, ['Transfer Bank', 'QRIS'], true)) {
            return back()->with('error', 'Bukti pembayaran hanya dapat diunggah untuk pesanan Transfer Bank/QRIS yang masih Pending.');
        }

        $data = $request->validate([
            'payment_proof' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ], [
            'payment_proof.required' => 'Pilih gambar bukti pembayaran terlebih dahulu.',
            'payment_proof.image' => 'Bukti pembayaran harus berupa gambar.',
            'payment_proof.mimes' => 'Format bukti pembayaran harus JPG, JPEG, PNG, atau WEBP.',
            'payment_proof.max' => 'Ukuran bukti pembayaran maksimal 5 MB.',
        ]);

        // Bukti disimpan pada local/private disk agar tidak dapat diakses publik tanpa login.
        $newPath = $data['payment_proof']->store('payment-proofs/' . $penjualan->user_id, 'local');
        $oldPath = $penjualan->payment_proof_path;
        $penjualan->update(['payment_proof_path' => $newPath]);

        if ($oldPath) {
            Storage::disk('local')->delete($oldPath);
        }

        return back()->with('success', 'Bukti pembayaran berhasil diunggah. Toko akan memeriksanya.');
    }

    /** Tampilkan bukti hanya kepada pemilik pesanan. */
    public function paymentProof(Penjualan $penjualan)
    {
        $this->authorizeOwner($penjualan);

        abort_unless($penjualan->payment_proof_path, 404);
        abort_unless(Storage::disk('local')->exists($penjualan->payment_proof_path), 404);

        return response()->file(Storage::disk('local')->path($penjualan->payment_proof_path), [
            'Cache-Control' => 'private, no-store, max-age=0',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /** Tampilkan foto bukti penerimaan hanya kepada pemilik pesanan. */
    public function deliveryProof(Penjualan $penjualan)
    {
        $this->authorizeOwner($penjualan);

        abort_unless($penjualan->status === 'Completed' && $penjualan->bukti_penerimaan_path, 404);
        abort_unless(Storage::disk('local')->exists($penjualan->bukti_penerimaan_path), 404);

        return response()->file(Storage::disk('local')->path($penjualan->bukti_penerimaan_path), [
            'Cache-Control' => 'private, no-store, max-age=0',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function cancel(Penjualan $penjualan)
    {
        $this->authorizeOwner($penjualan);

        if (! $penjualan->cancelAndRestock()) {
            return back()->with('error', 'Pesanan ini sudah diproses toko dan tidak bisa dibatalkan lagi.');
        }

        return back()->with('success', 'Pesanan dibatalkan dan stok buku dikembalikan.');
    }

    private function authorizeOwner(Penjualan $penjualan): void
    {
        abort_unless($penjualan->user_id === Auth::id() && $penjualan->isOnline(), 404);
    }
}
