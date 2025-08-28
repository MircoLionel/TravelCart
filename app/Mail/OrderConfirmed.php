<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Dompdf\Dompdf;
use Dompdf\Options;

class OrderConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order) {}

    public function build()
    {
        // Renderizar Blade del PDF a HTML
        $html = view('pdf.order', [
            'order' => $this->order->load(['user','items.tour','items.tourDate','reservation'])
        ])->render();

        // Dompdf
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $pdf = $dompdf->output();

        return $this->subject('Confirmación de reserva '.$this->order->code)
            ->markdown('mail.orders.confirmed', [
                'order' => $this->order
            ])
            ->attachData($pdf, "voucher-{$this->order->code}.pdf", [
                'mime' => 'application/pdf'
            ]);
    }
}
