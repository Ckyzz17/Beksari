<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMenuRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'kategori_id' => 'sometimes|exists:kategoris,id',
            'nama'        => 'sometimes|string|max:100',
            'harga'       => 'sometimes|numeric|min:0',
            'deskripsi'   => 'nullable|string',
            'tersedia'    => 'sometimes|boolean',
        ];
    }
}
