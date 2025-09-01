<?php

namespace App\Mail;

use App\Models\Order;
use App\Services\VoucherPdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order) {}

    public function build(VoucherPdf $voucherPdf)
    {
        $order = $this->order->load(['items.tour', 'items.tourDate', 'user']);
        $pdf   = $voucherPdf->render($order);

        return $this->subject('Tu reserva ' . $order->code . ' fue confirmada')
            ->view('emails.orders.confirmed', compact('order'))
            ->attachData($pdf, 'voucher-'.$order->code.'.pdf', ['mime' => 'application/pdf']);
    }
}
