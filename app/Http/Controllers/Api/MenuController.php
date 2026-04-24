<?php
namespace App\Http\Controllers\Api;

use App\Models\Menu;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMenuRequest;
use App\Http\Requests\UpdateMenuRequest;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::with('kategori')->get();

        return response()->json([
            'success' => true,
            'data'    => $menus
        ]);
    }

    public function show($id)
    {
        $menu = Menu::with('kategori')->find($id);

        if (!$menu) {
            return response()->json([
                'success' => false,
                'message' => 'Menu tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $menu
        ]);
    }

    public function store(StoreMenuRequest $request)
    {
        $menu = Menu::create($request->all());//validated

        return response()->json([
            'success' => true,
            'message' => 'Menu berhasil ditambahkan',
            'data'    => $menu->load('kategori') 
        ], 201);
    }

    public function update(UpdateMenuRequest $request, $id)
    {
        $menu = Menu::find($id);

        if (!$menu) {
            return response()->json([
                'success' => false,
                'message' => 'Menu tidak ditemukan'
            ], 404);
        }

        $menu->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Menu berhasil diperbarui',
            'data'    => $menu->fresh()->load('kategori')
        ]);
    }

    public function destroy($id)
    {
        $menu = Menu::find($id);

        if (!$menu) {
            return response()->json([
                'success' => false,
                'message' => 'Menu tidak ditemukan'
            ], 404);
        }

        $menu->delete();

        return response()->json([
            'success' => true,
            'message' => 'Menu berhasil dihapus'
        ]);
    }
}
