<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use Illuminate\Http\Request;

class VendorTourController extends Controller
{
    public function index(Request $request)
    {
        $vendor = $request->user();
        $tours = $vendor->vendorTours()->orderByDesc('id')->get();

        return view('vendor.tours.index', compact('tours'));
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
