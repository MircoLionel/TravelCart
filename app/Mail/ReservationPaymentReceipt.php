<?php

namespace App\Mail;

use App\Models\Reservation;
use App\Models\ReservationPayment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReservationPaymentReceipt extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Reservation $reservation, public ReservationPayment $payment, public string $pdf)
    {
    }

    public function build()
    {
        return $this
            ->subject('Comprobante de pago de reserva '.$this->reservation->locator)
            ->view('emails.reservations.payment_receipt')
            ->attachData($this->pdf, 'factura-'.$this->payment->id.'.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}
