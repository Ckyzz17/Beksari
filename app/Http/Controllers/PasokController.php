<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Distributor;
use App\Models\Pasok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PasokController extends Controller
{
    public function index()
    {
        $pasoks = Pasok::with(['distributor', 'buku'])->latest()->paginate(15);
        return view('pasok.index', compact('pasoks'));
    }

    public function create()
    {
        $distributors = Distributor::all();
        $bukus = Buku::where('status', 'publikasi')->get();
        return view('pasok.create', compact('distributors', 'bukus'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'distributor_id' => 'required|exists:distributors,id',
            'buku_id' => 'required|exists:bukus,id',
            'jumlah' => 'required|integer|min:1',
            'tanggal' => 'required|date',
        ]);

        DB::transaction(function () use ($validated) {
            Pasok::create($validated);
            // Tambahkan stok buku secara otomatis
            Buku::where('id', $validated['buku_id'])->increment('stok', $validated['jumlah']);
        });

        return redirect()->route('pasok.index')->with('success', 'Pasok buku berhasil dicatat dan stok gudang telah diperbarui!');
    }
}
