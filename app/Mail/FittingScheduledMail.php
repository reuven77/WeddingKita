<?php

namespace App\Mail;

use App\Models\Fitting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * FittingScheduledMail — email konfirmasi jadwal fitting di butik.
 * Dikirim ke member setelah jadwal fitting berhasil dibuat/diperbarui.
 * Lihat: 02-PRD.md — notifikasi email.
 */
class FittingScheduledMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  Fitting  $fitting  Data jadwal fitting yang baru dijadwalkan.
     */
    public function __construct(
        public readonly Fitting $fitting,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Jadwal Fitting Terkonfirmasi – WeddingKita 👗',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.fitting_scheduled',
        );
    }

    /**
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
