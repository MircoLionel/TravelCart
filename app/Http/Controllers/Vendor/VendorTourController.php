<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\ReservationPassenger;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class VendorTourController extends Controller
{
    public function index(Request $request)
    {
        $vendor = $request->user();
        $tours = $vendor->vendorTours()
            ->withCount(['reservations as sales_count' => function ($q) {
                $q->whereNull('reservations.deleted_at');
            }])
            ->withSum(['reservations as sales_amount' => function ($q) {
                $q->whereNull('reservations.deleted_at');
            }], 'total_amount')
            ->orderByDesc('id')
            ->get();

        $maxCount = max(1, (int) $tours->max('sales_count'));
        $maxAmount = max(1, (int) $tours->max('sales_amount'));

        return view('vendor.tours.index', compact('tours', 'maxCount', 'maxAmount'));
    }

    public function create()
    {
        return view('vendor.tours.create');
    }

    public function store(Request $request)
    {
        $vendor = $request->user();
        $data = $this->validatedData($request);
        $data['vendor_id'] = $vendor->id;
        $data['is_active'] = $request->boolean('is_active');

        $tour = Tour::create($data);

        return redirect()->route('vendor.tours.edit', $tour)->with('ok', 'Viaje creado.');
    }

    public function edit(Tour $tour)
    {
        $this->ensureOwnership($tour);
        $tour->load(['dates' => fn ($q) => $q->orderBy('start_date')]);
        return view('vendor.tours.edit', compact('tour'));
    }

    public function confirmDestroy(Tour $tour)
    {
        $this->ensureOwnership($tour);
        $tour->load(['reservations' => fn ($q) => $q->whereNull('reservations.deleted_at')->with('order.user')]);

        if ($tour->reservations->isEmpty()) {
            return redirect()->route('vendor.tours.edit', $tour);
        }

        return view('vendor.tours.confirm-delete', compact('tour'));
    }

    public function update(Request $request, Tour $tour)
    {
        $this->ensureOwnership($tour);
        $data = $this->validatedData($request);
        $data['is_active'] = $request->boolean('is_active');
        $tour->update($data);

        return back()->with('ok', 'Viaje actualizado.');
    }

    public function destroy(Tour $tour)
    {
        $this->ensureOwnership($tour);
        $hasReservations = $tour->reservations()->whereNull('reservations.deleted_at')->exists();

        if ($hasReservations && !request()->boolean('confirm')) {
            return redirect()->route('vendor.tours.confirm-delete', $tour)->with('warn', 'Este viaje tiene reservas activas. Confirma para enviarlo a la papelera.');
        }

        DB::transaction(function () use ($tour) {
            $reservations = $tour->reservations()
                ->whereNull('reservations.deleted_at')
                ->with(['order', 'tourDate'])
                ->get();

            foreach ($reservations as $reservation) {
                if ($reservation->tourDate) {
                    $reservation->tourDate()->increment('available', $reservation->qty);
                }

                $reservation->status = 'cancelled';
                $reservation->save();
                $reservation->delete();

                if ($order = $reservation->order) {
                    $order->status = 'cancelled';
                    $order->save();

                    if ($order->reservations()->whereNull('reservations.deleted_at')->doesntExist()) {
                        $order->delete();
                    }
                }
            }

            $tour->dates()->delete();
            $tour->delete();
        });

        return redirect()->route('vendor.tours.trash')->with('ok', 'Viaje enviado a la papelera.');
    }

    public function trash(Request $request)
    {
        $vendor = $request->user();
        $tours = $vendor->vendorTours()->onlyTrashed()->with(['dates' => fn ($q) => $q->withTrashed()])->get();

        return view('vendor.tours.trash', compact('tours'));
    }

    public function exportPassengers(Tour $tour)
    {
        $this->ensureOwnership($tour);

        $passengers = ReservationPassenger::query()
            ->selectRaw('tour_dates.start_date as start_date, reservations.locator, reservation_passengers.first_name, reservation_passengers.last_name, reservation_passengers.document_number, reservation_passengers.birth_date, reservation_passengers.sex, users.name as buyer_name, users.email as buyer_email')
            ->join('reservations', 'reservations.id', '=', 'reservation_passengers.reservation_id')
            ->leftJoin('tour_dates', 'tour_dates.id', '=', 'reservations.tour_date_id')
            ->leftJoin('orders', 'orders.id', '=', 'reservations.order_id')
            ->leftJoin('users', 'users.id', '=', 'orders.user_id')
            ->where('reservations.tour_id', $tour->id)
            ->whereNull('reservations.deleted_at')
            ->orderBy('tour_dates.start_date')
            ->orderBy('reservation_passengers.last_name')
            ->get();

        $lines = [
            'Fecha de salida\tLocalizador\tPasajero\tDocumento\tNacimiento\tSexo\tComprador\tEmail comprador',
        ];

        foreach ($passengers as $passenger) {
            $startDate = $passenger->start_date ? Carbon::parse($passenger->start_date)->format('Y-m-d') : '';
            $birthDate = $passenger->birth_date ? Carbon::parse($passenger->birth_date)->format('Y-m-d') : '';

            $lines[] = collect([
                $startDate,
                $passenger->locator,
                trim($passenger->first_name . ' ' . $passenger->last_name),
                $passenger->document_number,
                $birthDate,
                $passenger->sex,
                $passenger->buyer_name,
                $passenger->buyer_email,
            ])->implode("\t");
        }

        $content = implode("\n", $lines);
        $filename = 'pasajeros_' . Str::slug($tour->title ?: 'tour') . '.xls';

        return response($content)
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    public function exportTrashedPassengers($tourId)
    {
        $tour = Tour::withTrashed()->findOrFail($tourId);
        $this->ensureOwnership($tour);

        $reservations = Reservation::with(['passengers', 'payments', 'tourDate', 'order.user'])
            ->where('tour_id', $tour->id)
            ->get();

        $lines = [
            'Fecha de salida\tLocalizador\tPasajero\tDocumento\tPagado\tTotal reserva\tComprador\tEmail comprador',
        ];

        foreach ($reservations as $reservation) {
            $paid = $reservation->payments->sum('amount');
            $buyerName = optional($reservation->order)->user->name ?? '';
            $buyerEmail = optional($reservation->order)->user->email ?? '';
            $startDate = optional($reservation->tourDate?->start_date)->format('Y-m-d');

            foreach ($reservation->passengers as $passenger) {
                $lines[] = collect([
                    $startDate,
                    $reservation->locator,
                    trim($passenger->first_name . ' ' . $passenger->last_name),
                    $passenger->document_number,
                    number_format($paid, 0, ',', '.'),
                    number_format($reservation->total_amount, 0, ',', '.'),
                    $buyerName,
                    $buyerEmail,
                ])->implode("\t");
            }
        }

        $content = implode("\n", $lines);
        $filename = 'papelera_' . Str::slug($tour->title ?: 'tour') . '.xlsx';

        return response($content)
            ->header('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'base_price'  => 'required|numeric|min:0',
            'days'        => 'required|integer|min:1',
            'origin'      => 'nullable|string|max:255',
            'destination' => 'required|string|max:255',
            'is_active'   => 'boolean',
        ]);
    }

    private function ensureOwnership(Tour $tour): void
    {
        if ($tour->vendor_id !== auth()->id()) {
            abort(403);
        }
    }
}
