<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\TourDate;
use Illuminate\Http\Request;

class VendorTourDateController extends Controller
{
    public function store(Request $request, Tour $tour)
    {
        $this->ensureOwnership($tour);

        $data = $this->validatedData($request);
        $data['available'] = $data['available'] ?? $data['capacity'];

        $tour->dates()->create($data);

        return back()->with('ok', 'Fecha agregada al viaje.');
    }

    public function update(Request $request, Tour $tour, TourDate $date)
    {
        $this->ensureOwnership($tour);
        $this->ensureDateMatchesTour($tour, $date);

        $data = $this->validatedData($request, $date);

        if (($data['available'] ?? $date->available) > $data['capacity']) {
            return back()->with('error', 'La disponibilidad no puede superar la capacidad.');
        }

        $date->update($data);

        return back()->with('ok', 'Fecha actualizada.');
    }

    public function destroy(Tour $tour, TourDate $date)
    {
        $this->ensureOwnership($tour);
        $this->ensureDateMatchesTour($tour, $date);

        if ($date->reservations()->exists()) {
            return back()->with('error', 'No se puede eliminar una fecha con reservas asociadas.');
        }

        $date->delete();

        return back()->with('ok', 'Fecha eliminada.');
    }

    private function ensureOwnership(Tour $tour): void
    {
        if ($tour->vendor_id !== auth()->id()) {
            abort(403);
        }
    }

    private function ensureDateMatchesTour(Tour $tour, TourDate $date): void
    {
        if ($date->tour_id !== $tour->id) {
            abort(404);
        }
    }

    private function validatedData(Request $request, ?TourDate $date = null): array
    {
        return $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'capacity'   => 'required|integer|min:1',
            'available'  => 'nullable|integer|min:0',
            'price'      => 'required|numeric|min:0',
            'is_active'  => 'boolean',
        ]);
    }
}
