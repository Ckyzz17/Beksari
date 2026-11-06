<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BukuController extends Controller
{
    public function index(Request $request)
    {
        $query = Buku::query();

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%")
                  ->orWhere('penulis', 'like', "%{$search}%")
                  ->orWhere('kode', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $bukus = $query->latest()->paginate(10)->withQueryString();
        $categories = Buku::select('kategori')->distinct()->pluck('kategori');

        return view('buku.index', compact('bukus', 'categories'));
    }

    public function create()
    {
        $categories = ['Pengembangan Diri', 'Fiksi Sejarah', 'Fiksi / Novel', 'Teknologi Informasi', 'Bisnis & Manajemen', 'Buku Pelajaran', 'Komik'];
        return view('buku.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'isbn' => 'required|string|unique:bukus,isbn',
            'judul' => 'required|string|max:255',
            'kategori' => 'required|string',
            'penulis' => 'required|string',
            'penerbit' => 'nullable|string',
            'tahun_terbit' => 'nullable|integer',
            'harga_pokok' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|gte:harga_pokok',
            'diskon' => 'nullable|integer|min:0|max:100',
            'stok' => 'nullable|integer|min:0',
            'lokasi_rak' => 'nullable|string',
            'status' => 'required|in:publikasi,draf',
            'sinopsis' => 'nullable|string',
            'cover_url' => 'nullable|string',
        ]);

        $latestId = Buku::max('id') ?? 0;
        $validated['kode'] = 'BK-' . str_pad($latestId + 1, 3, '0', STR_PAD_LEFT);
        $validated['diskon'] = $validated['diskon'] ?? 0;
        $validated['stok'] = $validated['stok'] ?? 0;
        $validated['tahun_terbit'] = $validated['tahun_terbit'] ?? date('Y');

        Buku::create($validated);

        return redirect()->route('buku.index')->with('success', 'Buku berhasil ditambahkan!');
    }

    public function show(Buku $buku)
    {
        return view('buku.show', compact('buku'));
    }

    public function edit(Buku $buku)
    {
        $categories = ['Pengembangan Diri', 'Fiksi Sejarah', 'Fiksi / Novel', 'Teknologi Informasi', 'Bisnis & Manajemen', 'Buku Pelajaran', 'Komik'];
        return view('buku.edit', compact('buku', 'categories'));
    }

    public function update(Request $request, Buku $buku)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'kategori' => 'required|string',
            'penulis' => 'required|string',
            'penerbit' => 'nullable|string',
            'tahun_terbit' => 'nullable|integer',
            'harga_pokok' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|gte:harga_pokok',
            'diskon' => 'nullable|integer|min:0|max:100',
            'stok' => 'nullable|integer|min:0',
            'lokasi_rak' => 'nullable|string',
            'status' => 'required|in:publikasi,draf',
            'sinopsis' => 'nullable|string',
            'cover_url' => 'nullable|string',
        ]);

        $buku->update($validated);

        return redirect()->route('buku.show', $buku)->with('success', 'Perubahan data buku berhasil disimpan!');
    }

    public function destroy(Buku $buku)
    {
        if ($buku->detailPenjualans()->exists() || $buku->pasoks()->exists()) {
            return back()->with('error', 'Buku memiliki riwayat penjualan atau pasok. Ubah status menjadi draf agar riwayat tetap aman.');
        }
        $buku->delete();
        return redirect()->route('buku.index')->with('success', 'Buku berhasil dihapus.');
    }
}
