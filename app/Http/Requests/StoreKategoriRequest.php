<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreKategoriRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:50|unique:kategoris,nama',
        ];
    }

    public function messages(): array
    {
        return [
            'nama.unique' => 'Kategori dengan nama ini sudah ada',
        ];
    }
}
