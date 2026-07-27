<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * UpdateRentalStatusRequest — validasi perubahan status sewa oleh Admin.
 * Action yang valid: confirm, pickup, return, cancel.
 * Lihat: 03-RULES.md §4, DashboardController::updateRentalStatus().
 */
class UpdateRentalStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Gate-level otorisasi ditangani oleh middleware 'role:admin' di route.
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', 'string', 'in:confirm,pickup,return,cancel'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'status.required' => 'Action status wajib diisi.',
            'status.in'       => 'Status tidak valid. Pilihan: confirm, pickup, return, cancel.',
        ];
    }
}
