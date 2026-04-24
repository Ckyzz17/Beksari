<?php
namespace App\Http\Controllers\Api;

use App\Models\Kategori;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreKategoriRequest;

class KategoriController extends Controller
{
    public function index()
    {
        $kategoris = Kategori::withCount('menus')->get();

        return response()->json([
            'success' => true,
            'data'    => $kategoris
        ]);
    }

    public function store(StoreKategoriRequest $request)
    {
        $kategori = Kategori::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil ditambahkan',
            'data'    => $kategori
        ], 201);
    }

    public function destroy($id)
    {
        $kategori = Kategori::find($id);

        if (!$kategori) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan'
            ], 404);
        }

        if ($kategori->menus()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak bisa hapus kategori yang masih memiliki menu'
            ], 422);
        }

        $kategori->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil dihapus'
        ]);
    }
}