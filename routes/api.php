<?php

use App\Models\Buku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/books', function (Request $request) {
    $data = $request->validate([
        'q' => 'nullable|string|max:100',
        'page' => 'nullable|integer|min:1',
    ]);

    return Buku::query()
        ->where('status', 'publikasi')
        ->when($data['q'] ?? null, function ($query, $search) {
            $query->where(function ($nested) use ($search) {
                $nested->where('judul', 'like', "%{$search}%")
                    ->orWhere('isbn', 'like', "%{$search}%");
            });
        })
        ->select(['id', 'kode', 'isbn', 'judul', 'kategori', 'penulis', 'penerbit', 'tahun_terbit', 'harga_jual', 'diskon', 'stok', 'cover_url', 'sinopsis'])
        ->paginate(12);
})->middleware('throttle:60,1');
