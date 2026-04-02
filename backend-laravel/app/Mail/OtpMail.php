<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $code;

    /**
     * Yangi xabar nusxasini yaratish.
     */
    public function __construct($code)
    {
        $this->code = $code;
    }

    /**
     * Xat sarlavhasi (Mavzusi).
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Kirish kodi: ' . $this->code,
        );
    }

    /**
     * Xat mazmuni (HTML ko'rinishi).
     */
    public function content(): Content
    {
        return new Content(
            htmlString: "
                <div style='font-family:sans-serif;max-width:400px;margin:0 auto;border:1px solid #e2e8f0;border-radius:16px;padding:20px;text-align:center'>
                    <h2 style='color:#1e293b'>Kirish kodi</h2>
                    <div style='font-size:32px;font-weight:bold;letter-spacing:10px;color:#3b82f6;padding:15px;background:#f8fafc;border-radius:10px;margin:20px 0'>
                        {$this->code}
                    </div>
                    <p style='color:#64748b;font-size:14px'>Kod 5 daqiqa davomida amal qiladi.</p>
                </div>
            ",
        );
    }

    /**
     * Biriktirilgan fayllar.
     */
    public function attachments(): array
    {
        return [];
    }
}
