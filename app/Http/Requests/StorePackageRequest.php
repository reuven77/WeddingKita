<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePackageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:300'],
            'description' => ['nullable', 'string'],
            'category_id' => ['required', 'uuid', 'exists:categories,id'],
            'cover_image' => ['nullable', 'image', 'max:2048'], // max 2MB
            'base_price' => ['required', 'numeric', 'min:0'],
            'deposit_amount' => ['required', 'numeric', 'min:0'],
            'items' => ['required', 'array', 'min:1'],
            'items.*' => ['uuid', 'exists:items,id'],
            'services' => ['nullable', 'array'],
            'services.*' => ['uuid', 'exists:services,id'],
            'default_services' => ['nullable', 'array'],
            'default_services.*' => ['uuid', 'exists:services,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama paket wajib diisi.',
            'name.max' => 'Nama paket maksimal 300 karakter.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.uuid' => 'ID kategori tidak valid.',
            'category_id.exists' => 'Kategori tidak ditemukan.',
            'cover_image.image' => 'File harus berupa gambar.',
            'cover_image.max' => 'Ukuran gambar maksimal 2MB.',
            'base_price.required' => 'Harga sewa dasar paket wajib diisi.',
            'base_price.numeric' => 'Harga sewa dasar paket harus berupa angka.',
            'base_price.min' => 'Harga sewa dasar paket minimal 0.',
            'deposit_amount.required' => 'Jaminan/deposit wajib diisi.',
            'deposit_amount.numeric' => 'Jaminan/deposit harus berupa angka.',
            'deposit_amount.min' => 'Jaminan/deposit minimal 0.',
            'items.required' => 'Minimal satu item fisik harus disertakan dalam paket.',
            'items.array' => 'Item fisik harus dalam format array.',
            'items.min' => 'Pilih minimal satu item fisik.',
            'items.*.uuid' => 'ID item tidak valid.',
            'items.*.exists' => 'Item tidak ditemukan.',
            'services.array' => 'Layanan harus dalam format array.',
            'services.*.uuid' => 'ID layanan tidak valid.',
            'services.*.exists' => 'Layanan tidak ditemukan.',
        ];
    }
}
