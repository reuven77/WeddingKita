<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * UpdateFittingStatusRequest — validasi perubahan status fitting oleh Admin.
 * Action yang valid: complete, cancel, noshow.
 * Lihat: 03-RULES.md §4, DashboardController::updateFittingStatus().
 */
class UpdateFittingStatusRequest extends FormRequest
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
            'status' => ['required', 'string', 'in:complete,cancel,noshow'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'status.required' => 'Action status fitting wajib diisi.',
            'status.in'       => 'Status fitting tidak valid. Pilihan: complete, cancel, noshow.',
        ];
    }
}
