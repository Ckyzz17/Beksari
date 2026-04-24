<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            'kategori_id' => 'required|exists:kategoris,id', 
            'nama'        => 'required|string|max:100',
            'harga'       => 'required|numeric|min:0',
            'deskripsi'   => 'nullable|string',
            'tersedia'    => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'kategori_id.required' => 'Kategori wajib dipilih',
            'kategori_id.exists'   => 'Kategori tidak ditemukan',
            'nama.required'        => 'Nama menu wajib diisi',
            'harga.required'       => 'Harga wajib diisi',
            'harga.numeric'        => 'Harga harus berupa angka',
            'harga.min'            => 'Harga tidak boleh negatif',
        ];
    }
}
