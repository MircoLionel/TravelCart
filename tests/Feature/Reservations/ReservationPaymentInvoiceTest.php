<?php

namespace Tests\Feature\Reservations;

use App\Mail\ReservationPaymentReceipt;
use App\Models\Reservation;
use App\Models\ReservationPayment;
use App\Models\Order;
use App\Models\Tour;
use App\Models\TourDate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ReservationPaymentInvoiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoice_pdf_includes_commission_breakdown(): void
    {
        $vendor = User::factory()->vendor()->create();
        $buyer = User::factory()->create(['name' => 'Ana Compradora']);
        $tour = Tour::factory()->create(['vendor_id' => $vendor->id, 'base_price' => 10000]);
        $date = TourDate::factory()->create(['tour_id' => $tour->id]);
        $order = Order::factory()->for($buyer)->create();

        $reservation = Reservation::factory()->create([
            'order_id'     => $order->id,
            'vendor_id'     => $vendor->id,
            'tour_id'       => $tour->id,
            'tour_date_id'  => $date->id,
            'total_amount'  => 10000,
            'qty'           => 1,
        ]);

        $payment = ReservationPayment::create([
            'reservation_id' => $reservation->id,
            'vendor_id'      => $vendor->id,
            'amount'         => 2000,
        ]);

        $html = app('view')->make('pdf.payment_invoice', [
            'reservation' => $reservation,
            'payment' => $payment,
        ])->render();

        $this->assertStringContainsString('13%', $html);
        $this->assertStringContainsString('8.700', $html); // neto a proveedor
        $this->assertStringContainsString('Ana Compradora', $html); // titular visible
    }

    public function test_manual_payment_sends_invoice_to_vendor(): void
    {
        Mail::fake();

        $vendor = User::factory()->vendor()->create();
        $tour = Tour::factory()->create(['vendor_id' => $vendor->id]);
        $date = TourDate::factory()->create(['tour_id' => $tour->id]);
        $reservation = Reservation::factory()->create([
            'vendor_id'     => $vendor->id,
            'tour_id'       => $tour->id,
            'tour_date_id'  => $date->id,
            'total_amount'  => 5000,
            'qty'           => 1,
        ]);

        $this->actingAs($vendor)
            ->post(route('vendor.reservations.payments', $reservation), [
                'amount' => 1000,
            ])->assertRedirect();

        Mail::assertSent(ReservationPaymentReceipt::class, 1);
    }
}
