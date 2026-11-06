<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'store_name' => Setting::value('store_name', 'SMART BOOKS Indonesia'),
            'store_address' => Setting::value('store_address', 'Jl. Literasi Digital No. 123'),
            'store_phone' => Setting::value('store_phone', ''),
            'whatsapp' => Setting::value('whatsapp', ''),
            'bank_name' => Setting::value('bank_name', ''),
            'bank_account' => Setting::value('bank_account', ''),
            'bank_holder' => Setting::value('bank_holder', ''),
            'qris_info' => Setting::value('qris_info', 'Silakan scan QRIS toko untuk membayar.'),
            'qris_image_path' => Setting::value('qris_image_path'),
            'shipping_flat' => Setting::value('shipping_flat', 0),
            'tax_percentage' => 11,
            'receipt_footer' => Setting::value('receipt_footer', 'Terima kasih telah berbelanja!'),
        ];

        return view('setting.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'store_name' => 'required|string|max:150',
            'store_address' => 'nullable|string|max:500',
            'store_phone' => 'nullable|string|max:30',
            'whatsapp' => 'nullable|string|max:30',
            'bank_name' => 'nullable|string|max:100',
            'bank_account' => 'nullable|string|max:100',
            'bank_holder' => 'nullable|string|max:150',
            'qris_info' => 'nullable|string|max:500',
            'shipping_flat' => 'required|integer|min:0',
            'receipt_footer' => 'nullable|string|max:255',
            'qris_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'remove_qris_image' => 'nullable|boolean',
        ]);

        unset($data['qris_image'], $data['remove_qris_image']);

        foreach ($data as $key => $value) {
            Setting::put($key, $value ?? '');
        }

        $oldQrisPath = Setting::value('qris_image_path');

        if ($request->hasFile('qris_image')) {
            $newQrisPath = $request->file('qris_image')->store('qris', 'public');
            Setting::put('qris_image_path', $newQrisPath);

            if ($oldQrisPath) {
                Storage::disk('public')->delete($oldQrisPath);
            }
        } elseif ($request->boolean('remove_qris_image')) {
            Setting::put('qris_image_path', '');

            if ($oldQrisPath) {
                Storage::disk('public')->delete($oldQrisPath);
            }
        }

        return back()->with('success', 'Pengaturan toko berhasil disimpan.');
    }
}
