<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * StoreRentalRequest — validasi data booking sewa busana pengantin.
 *
 * Aturan validasi sesuai field yang digunakan oleh RentalController::store()
 * dan kemudian diproses oleh RentalService & FittingService.
 * Lihat: 03-RULES.md §4.
 */
class StoreRentalRequest extends FormRequest
{
    /**
     * Tentukan apakah user berhak membuat request ini.
     * Autentikasi sudah ditangani oleh middleware 'auth' di route.
     */
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
            'item_id'        => ['nullable', 'uuid', 'exists:items,id'],
            'package_id'     => ['nullable', 'uuid', 'exists:packages,id'],
            'event_date'     => ['required', 'date', 'after_or_equal:today'],
            'scheduled_date' => ['required', 'date', 'after_or_equal:today', 'before_or_equal:event_date'],
            'scheduled_time' => ['required', 'string', 'regex:/^\d{2}:\d{2}$/'],
            'addon_services' => ['nullable', 'array'],
            'addon_services.*' => ['uuid', 'exists:services,id'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'item_id.uuid'            => 'Format ID item tidak valid.',
            'item_id.exists'          => 'Item yang dipilih tidak ditemukan dalam katalog.',
            'package_id.uuid'         => 'Format ID paket tidak valid.',
            'package_id.exists'       => 'Paket yang dipilih tidak ditemukan.',
            'event_date.required'     => 'Tanggal acara wajib diisi.',
            'event_date.date'         => 'Format tanggal acara tidak valid.',
            'event_date.after_or_equal' => 'Tanggal acara tidak boleh di masa lalu.',
            'scheduled_date.required' => 'Tanggal jadwal fitting wajib diisi.',
            'scheduled_date.date'     => 'Format tanggal fitting tidak valid.',
            'scheduled_date.after_or_equal' => 'Tanggal fitting tidak boleh di masa lalu.',
            'scheduled_date.before_or_equal' => 'Tanggal fitting harus sebelum atau tepat pada hari acara.',
            'scheduled_time.required' => 'Jam fitting wajib dipilih.',
            'scheduled_time.regex'    => 'Format jam fitting harus HH:MM (misal: 09:00).',
            'addon_services.array'    => 'Format layanan tambahan tidak valid.',
            'addon_services.*.uuid'   => 'Format ID layanan tambahan tidak valid.',
            'addon_services.*.exists' => 'Salah satu layanan tambahan yang dipilih tidak tersedia.',
        ];
    }

    /**
     * Validasi tambahan: minimal salah satu antara item_id atau package_id harus diisi.
     * Dipanggil setelah rules() lolos.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     */
    public function withValidator(\Illuminate\Validation\Validator $validator): void
    {
        $validator->after(function (\Illuminate\Validation\Validator $v) {
            if (! $this->input('item_id') && ! $this->input('package_id')) {
                $v->errors()->add('item_id', 'Silakan pilih gaun/jas tunggal atau paket lengkap.');
            }
        });
    }
}
