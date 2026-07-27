<?php

namespace App\Mail;

use App\Models\Fitting;
use App\Models\Rental;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * RentalConfirmedMail — email konfirmasi penyewaan busana pengantin.
 * Dikirim ke member setelah transaksi sewa berhasil dibuat.
 * Lihat: 02-PRD.md — notifikasi email.
 */
class RentalConfirmedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  Rental   $rental   Data transaksi sewa yang baru dibuat.
     * @param  Fitting  $fitting  Data jadwal fitting yang terhubung.
     */
    public function __construct(
        public readonly Rental $rental,
        public readonly Fitting $fitting,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Konfirmasi Pemesanan Sewa Busana – WeddingKita 💐',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.rental_confirmed',
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
