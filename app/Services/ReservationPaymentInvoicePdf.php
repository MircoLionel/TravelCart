<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\ReservationPayment;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Contracts\View\Factory as ViewFactory;

class ReservationPaymentInvoicePdf
{
    public function __construct(private ViewFactory $view)
    {
    }

    public function render(Reservation $reservation, ReservationPayment $payment): string
    {
        $html = $this->view->make('pdf.payment_invoice', [
            'reservation' => $reservation,
            'payment'     => $payment,
        ])->render();

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }
}
