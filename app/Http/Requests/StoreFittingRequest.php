<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * StoreFittingRequest — validasi data booking jadwal fitting mandiri.
 * Digunakan jika member ingin booking fitting tanpa melakukan sewa item baru.
 * Lihat: 03-RULES.md §4.
 */
class StoreFittingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'rental_id'      => ['nullable', 'uuid', 'exists:rentals,id'],
            'item_id'        => ['nullable', 'uuid', 'exists:items,id'],
            'package_id'     => ['nullable', 'uuid', 'exists:packages,id'],
            'scheduled_date' => ['required', 'date', 'after_or_equal:today'],
            'scheduled_time' => ['required', 'string', 'regex:/^\d{2}:\d{2}$/'],
            'duration_minutes' => ['nullable', 'integer', 'min:30', 'max:180'],
            'notes'          => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'rental_id.uuid'            => 'Format ID rental tidak valid.',
            'rental_id.exists'          => 'Transaksi sewa yang direferensikan tidak ditemukan.',
            'item_id.uuid'              => 'Format ID item tidak valid.',
            'item_id.exists'            => 'Item yang dipilih tidak ditemukan.',
            'package_id.uuid'           => 'Format ID paket tidak valid.',
            'package_id.exists'         => 'Paket yang dipilih tidak ditemukan.',
            'scheduled_date.required'   => 'Tanggal fitting wajib diisi.',
            'scheduled_date.after_or_equal' => 'Tanggal fitting tidak boleh di masa lalu.',
            'scheduled_time.required'   => 'Jam fitting wajib dipilih.',
            'scheduled_time.regex'      => 'Format jam fitting harus HH:MM (misal: 10:00).',
            'duration_minutes.integer'  => 'Durasi fitting harus berupa angka menit.',
            'duration_minutes.min'      => 'Durasi minimum fitting adalah 30 menit.',
            'duration_minutes.max'      => 'Durasi maksimum fitting adalah 180 menit.',
            'notes.max'                 => 'Catatan tidak boleh lebih dari 500 karakter.',
        ];
    }
}
