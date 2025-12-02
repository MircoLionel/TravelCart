<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\ReservationPassenger;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

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
        $tour->delete();

        return back()->with('ok', 'Viaje eliminado.');
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
