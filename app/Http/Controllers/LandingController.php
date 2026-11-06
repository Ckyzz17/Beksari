<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index(Request $request)
    {
        $query = Buku::where('status', 'publikasi');

        if ($request->filled('kategori') && $request->kategori !== 'Semua') {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('penulis', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%");
            });
        }

        match ($request->input('urut', 'terbaru')) {
            'termurah' => $query->orderByRaw('(harga_jual * (100 - diskon)) ASC'),
            'termahal' => $query->orderByRaw('(harga_jual * (100 - diskon)) DESC'),
            default => $query->latest(),
        };
        $books = $query->paginate(12)->withQueryString();
        $categories = Buku::where('status', 'publikasi')->distinct()->orderBy('kategori')->pluck('kategori');

        return view('landing', compact('books', 'categories'));
    }

    public function show(Buku $buku)
    {
        abort_unless($buku->status === 'publikasi', 404);

        return view('toko.show', compact('buku'));
    }
}
