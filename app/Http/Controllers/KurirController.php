<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class KurirController extends Controller
{
    public function index()
    {
        $pesananAktif = Penjualan::with('user')
            ->where('tipe', 'online')
            ->where('kurir_id', Auth::id())
            ->whereIn('status', ['Diproses', 'Dikirim'])
            ->orderByRaw("CASE WHEN status = 'Dikirim' THEN 0 ELSE 1 END")
            ->latest('kurir_assigned_at')
            ->get();

        $pesananSelesai = Penjualan::with('user')
            ->where('tipe', 'online')
            ->where('kurir_id', Auth::id())
            ->where('status', 'Completed')
            ->latest('diterima_pada')
            ->take(10)
            ->get();

        $selesaiHariIni = Penjualan::where('tipe', 'online')
            ->where('kurir_id', Auth::id())
            ->where('status', 'Completed')
            ->whereDate('diterima_pada', today())
            ->count();

        // Uang tunai yang harus ditagih kurir dari pesanan COD yang belum diserahkan.
        $tagihanCod = (int) $pesananAktif->where('metode_pembayaran', 'COD')->sum('total_bayar');

        return view('kurir.index', compact('pesananAktif', 'pesananSelesai', 'selesaiHariIni', 'tagihanCod'));
    }

    public function show(Penjualan $penjualan)
    {
        $this->ensureAssigned($penjualan);
        $penjualan->load(['user', 'details.buku', 'kurir']);

        return view('kurir.show', compact('penjualan'));
    }

    public function updateStatus(Request $request, Penjualan $penjualan)
    {
        $this->ensureAssigned($penjualan);

        $data = $request->validate([
            'status' => 'required|in:Dikirim,Completed',
            'bukti_penerimaan' => 'required_if:status,Completed|nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ], [
            'bukti_penerimaan.required_if' => 'Upload foto barang di depan penerima sebelum menyelesaikan pengantaran.',
            'bukti_penerimaan.image' => 'Bukti penerimaan harus berupa gambar.',
            'bukti_penerimaan.mimes' => 'Format foto harus JPG, JPEG, PNG, atau WEBP.',
            'bukti_penerimaan.max' => 'Ukuran foto maksimal 5 MB.',
        ]);

        $proofPath = null;
        if ($data['status'] === 'Completed') {
            $proofPath = $request->file('bukti_penerimaan')->store('delivery-proofs/' . $penjualan->id, 'local');
        }

        try {
            $result = DB::transaction(function () use ($penjualan, $data, $proofPath) {
                $current = Penjualan::query()->lockForUpdate()->findOrFail($penjualan->id);

                if ((int) $current->kurir_id !== (int) Auth::id()) {
                    return ['ok' => false, 'message' => 'Pesanan ini bukan tugas akun Anda.'];
                }

                $allowed = [
                    'Diproses' => 'Dikirim',
                    'Dikirim' => 'Completed',
                ];

                if (($allowed[$current->status] ?? null) !== $data['status']) {
                    return ['ok' => false, 'message' => 'Status pesanan sudah berubah atau urutan status tidak sesuai.'];
                }

                $updates = ['status' => $data['status']];
                if ($data['status'] === 'Dikirim') {
                    $updates['dikirim_pada'] = now();
                } else {
                    $updates['diterima_pada'] = now();
                    $updates['bukti_penerimaan_path'] = $proofPath;
                    $updates['bukti_penerimaan_uploaded_at'] = now();
                    $updates['bayar'] = $current->total_bayar;
                    $updates['kembali'] = 0;
                }

                $current->update($updates);

                return ['ok' => true];
            });
        } catch (Throwable $exception) {
            if ($proofPath) {
                Storage::disk('local')->delete($proofPath);
            }
            throw $exception;
        }

        if (! $result['ok']) {
            if ($proofPath) {
                Storage::disk('local')->delete($proofPath);
            }

            return back()->with('error', $result['message']);
        }

        $message = $data['status'] === 'Dikirim'
            ? 'Pengantaran dimulai. Status pesanan menjadi Dikirim.'
            : 'Foto bukti penerimaan tersimpan dan pesanan berhasil diselesaikan.';

        return redirect()->route('kurir.pesanan.show', $penjualan)->with('success', $message);
    }

    private function ensureAssigned(Penjualan $penjualan): void
    {
        abort_unless(
            $penjualan->tipe === 'online' && (int) $penjualan->kurir_id === (int) Auth::id(),
            403,
            'Pesanan ini bukan tugas akun kurir Anda.'
        );
    }
}
