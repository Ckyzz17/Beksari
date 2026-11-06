<?php

namespace App\Http\Controllers;

use App\Models\Distributor;
use Illuminate\Http\Request;

class DistributorController extends Controller
{
    public function index(Request $request)
    {
        $query = Distributor::query();
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('kode', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        $distributors = $query->latest()->paginate(10)->withQueryString();
        return view('distributor.index', compact('distributors'));
    }

    public function create()
    {
        return view('distributor.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:150',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:100',
        ]);

        $latestId = Distributor::max('id') ?? 0;
        $validated['kode'] = 'DST-' . str_pad($latestId + 1, 3, '0', STR_PAD_LEFT);

        Distributor::create($validated);
        return redirect()->route('distributor.index')->with('success', 'Distributor baru berhasil ditambahkan!');
    }

    public function edit(Distributor $distributor)
    {
        return view('distributor.edit', compact('distributor'));
    }

    public function update(Request $request, Distributor $distributor)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:150',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:100',
        ]);

        $distributor->update($validated);
        return redirect()->route('distributor.index')->with('success', 'Data distributor berhasil diperbarui!');
    }

    public function destroy(Distributor $distributor)
    {
        $distributor->delete();
        return redirect()->route('distributor.index')->with('success', 'Distributor berhasil dihapus.');
    }
}
