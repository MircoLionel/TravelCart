<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\ReservationPayment;
use App\Models\TourDate;
use App\Models\Order;
use App\Mail\ReservationPaymentReceipt;
use App\Services\ReservationPaymentInvoicePdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class VendorReservationController extends Controller
{
    public function __construct(private ReservationPaymentInvoicePdf $invoicePdf)
    {
    }

    public function index(Request $request)
    {
        $vendor = $request->user();
        $query = Reservation::with(['tour', 'tourDate', 'passengers'])
            ->where('vendor_id', $vendor->id)
            ->orderByDesc('created_at');

        if ($search = trim($request->query('q', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('locator', 'like', "%{$search}%")
                  ->orWhereHas('passengers', function ($qq) use ($search) {
                      $qq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%")
                         ->orWhere('document_number', 'like', "%{$search}%");
                  });
            });
        }

        $reservations = $query->paginate(20);

        return view('vendor.reservations.index', compact('reservations', 'search'));
    }

    public function show(Reservation $reservation)
    {
        $this->ensureOwnership($reservation);
        $reservation->load(['tour', 'tourDate', 'passengers', 'payments']);

        return view('vendor.reservations.show', compact('reservation'));
    }

    public function update(Request $request, Reservation $reservation)
    {
        $this->ensureOwnership($reservation);

        $data = $request->validate([
            'status' => 'required|in:awaiting_passengers,pending_payment,pending,confirmed,cancelled',
            'qty'    => 'required|integer|min:1',
        ]);

        try {
            DB::transaction(function () use ($reservation, $data) {
                $difference = $data['qty'] - $reservation->qty;

                if ($difference !== 0 && $reservation->tourDate) {
                    $tourDate = TourDate::lockForUpdate()->find($reservation->tour_date_id);
                    if ($difference > 0) {
                        if ($tourDate->available < $difference) {
                            throw new \RuntimeException('No hay disponibilidad para aumentar la reserva.');
                        }
                        $tourDate->decrement('available', $difference);
                    } else {
                        $tourDate->increment('available', abs($difference));
                    }
                }

                if ($data['status'] === 'cancelled' && $reservation->status !== 'cancelled' && $reservation->tourDate) {
                    $reservation->tourDate()->increment('available', $reservation->qty);
                }

                if ($data['status'] === 'confirmed' && $reservation->passengers()->count() < $data['qty']) {
                    throw new \RuntimeException('Necesitás todos los pasajeros cargados antes de confirmar.');
                }

                $reservation->fill($data);
                $reservation->save();

                $this->syncOrderPaymentStatus($reservation->order);
            });
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('ok', 'Reserva actualizada.');
    }

    public function destroy(Reservation $reservation)
    {
        $this->ensureOwnership($reservation);
        if ($reservation->tourDate) {
            $reservation->tourDate()->increment('available', $reservation->qty);
        }
        $reservation->delete();

        $this->syncOrderPaymentStatus($reservation->order);

        return redirect()->route('vendor.reservations.index')->with('ok', 'Reserva eliminada y disponibilidad liberada.');
    }

    public function storePayment(Request $request, Reservation $reservation)
    {
        $this->ensureOwnership($reservation);

        $data = $request->validate([
            'amount' => 'required|integer|min:1',
            'note'   => 'nullable|string|max:255',
        ]);

        $payment = ReservationPayment::create([
            'reservation_id' => $reservation->id,
            'vendor_id'      => $request->user()->id,
            'amount'         => $data['amount'],
            'note'           => $data['note'] ?? null,
        ]);

        $reservation->load('order');
        if ($reservation->total_amount && $reservation->paidAmount() >= $reservation->total_amount) {
            $reservation->status = 'confirmed';
            $reservation->save();
        }

        $this->syncOrderPaymentStatus($reservation->order);

        if ($reservation->vendor && $payment) {
            $reservation->loadMissing(['tour', 'tourDate', 'vendor']);
            $pdf = $this->invoicePdf->render($reservation, $payment);
            Mail::to($reservation->vendor->email)->send(new ReservationPaymentReceipt($reservation, $payment, $pdf));
        }

        return back()->with('ok', 'Pago parcial registrado.');
    }

    private function ensureOwnership(Reservation $reservation): void
    {
        if ($reservation->vendor_id !== auth()->id()) {
            abort(403);
        }
    }

    private function syncOrderPaymentStatus(?Order $order): void
    {
        if (!$order) {
            return;
        }

        $order->load('reservations');

        if ($order->reservations->every(fn ($r) => $r->status === 'confirmed')) {
            $order->status = 'paid';
        } elseif ($order->reservations->contains(fn ($r) => $r->status === 'awaiting_passengers')) {
            $order->status = 'awaiting_passengers';
        } else {
            $order->status = 'pending_payment';
        }

        $order->save();
    }
}
