<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'uuid', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:300'],
            'description' => ['nullable', 'string'],
            'call_code' => ['required', 'string', 'max:20', 'unique:items,call_code'],
            'size_label' => ['required', 'string', 'max:100'],
            'cover_image' => ['nullable', 'image', 'max:2048'], // max 2MB
            'base_price' => ['required', 'numeric', 'min:0'],
            'deposit_amount' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'string', Rule::in(['aktif', 'maintenance', 'nonaktif'])],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.uuid' => 'ID kategori tidak valid.',
            'category_id.exists' => 'Kategori tidak ditemukan.',
            'name.required' => 'Nama item wajib diisi.',
            'name.max' => 'Nama item maksimal 300 karakter.',
            'call_code.required' => 'Kode panggilan (Call Code) wajib diisi.',
            'call_code.max' => 'Kode panggilan maksimal 20 karakter.',
            'call_code.unique' => 'Kode panggilan sudah terdaftar.',
            'size_label.required' => 'Label ukuran (Size) wajib diisi.',
            'size_label.max' => 'Label ukuran maksimal 100 karakter.',
            'cover_image.image' => 'File harus berupa gambar.',
            'cover_image.max' => 'Ukuran gambar maksimal 2MB.',
            'base_price.required' => 'Harga sewa dasar wajib diisi.',
            'base_price.numeric' => 'Harga sewa dasar harus berupa angka.',
            'base_price.min' => 'Harga sewa dasar minimal 0.',
            'deposit_amount.required' => 'Jaminan/deposit wajib diisi.',
            'deposit_amount.numeric' => 'Jaminan/deposit harus berupa angka.',
            'deposit_amount.min' => 'Jaminan/deposit minimal 0.',
            'status.required' => 'Status wajib diisi.',
            'status.in' => 'Status tidak valid. Pilihan: aktif, maintenance, nonaktif.',
        ];
    }
}
