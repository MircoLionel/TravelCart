<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\ReservationPassenger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReservationPassengerController extends Controller
{
    public function edit(Reservation $reservation)
    {
        $this->authorizeAccess($reservation);

        if ($this->expired($reservation)) {
            $this->expire($reservation);
            return redirect()->route('orders.show', $reservation->order_id)
                ->with('error', 'Venció la ventana de 10 minutos para cargar pasajeros.');
        }

        $reservation->load('passengers');

        return view('reservations.passengers', compact('reservation'));
    }

    public function store(Request $request, Reservation $reservation)
    {
        $this->authorizeAccess($reservation);

        if ($this->expired($reservation)) {
            $this->expire($reservation);
            return back()->with('error', 'La reserva expiró, solicitá una nueva.');
        }

        $data = $request->validate([
            'passengers'   => 'required|array|min:1',
            'passengers.*.first_name'      => 'required|string|max:255',
            'passengers.*.last_name'       => 'required|string|max:255',
            'passengers.*.document_number' => 'required|string|max:50',
            'passengers.*.birth_date'      => 'nullable|date',
            'passengers.*.sex'             => 'nullable|string|max:10',
        ]);

        DB::transaction(function () use ($reservation, $data) {
            foreach ($data['passengers'] as $passengerData) {
                ReservationPassenger::updateOrCreate(
                    [
                        'reservation_id'  => $reservation->id,
                        'document_number' => $passengerData['document_number'],
                    ],
                    [
                        'first_name' => $passengerData['first_name'],
                        'last_name'  => $passengerData['last_name'],
                        'birth_date' => $passengerData['birth_date'] ?? null,
                        'sex'        => $passengerData['sex'] ?? null,
                    ]
                );
            }
        });

        return back()->with('ok', 'Pasajeros guardados.');
    }

    private function authorizeAccess(Reservation $reservation): void
    {
        if ($reservation->order?->user_id !== auth()->id()) {
            abort(403);
        }
    }

    private function expired(Reservation $reservation): bool
    {
        return $reservation->hold_expires_at && now()->greaterThan($reservation->hold_expires_at);
    }

    private function expire(Reservation $reservation): void
    {
        if ($reservation->status !== 'cancelled' && $reservation->tourDate) {
            $reservation->tourDate()->increment('available', $reservation->qty);
        }
        $reservation->status = 'cancelled';
        $reservation->save();
    }
}
