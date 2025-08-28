<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\TourDate;
use Illuminate\Http\Request;

class TourDateAdminController extends Controller
{
    /**
     * Preserva los query params (status, q) al volver/redirigir.
     */
    private function backQuery(Request $r): array
    {
        return $r->only('status', 'q');
    }

    /**
     * Form de creación de fecha para un Tour.
     * Ruta: admin/tours/{tour}/dates/create
     */
    public function create(Request $r, Tour $tour)
    {
        return view('admin.dates.create', compact('tour'));
    }

    /**
     * Guarda una nueva fecha para el Tour.
     * Ruta: POST admin/tours/{tour}/dates
     */
    public function store(Request $r, Tour $tour)
    {
        $data = $r->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'capacity'   => 'required|integer|min:1',
            'available'  => 'required|integer|min:0',
            'price'      => 'required|numeric|min:0',
            'is_active'  => 'boolean',
        ]);
        $data['is_active'] = $r->boolean('is_active');

        if ($data['available'] > $data['capacity']) {
            return back()->withInput()->withErrors([
                'available' => 'Disponible no puede superar la capacidad.',
            ]);
        }

        $tour->dates()->create($data);

        return redirect()->route(
            'admin.tours.edit',
            array_merge(['tour' => $tour->id], $this->backQuery($r))
        )->with('ok', 'Fecha creada.');
    }

    /**
     * Form de edición de una fecha (shallow).
     * Ruta: admin/dates/{date}/edit
     */
    public function edit(Request $r, $id)
    {
        $date = TourDate::withTrashed()->findOrFail($id);
        $tour = $date->tour()->withTrashed()->first();

        return view('admin.dates.edit', compact('tour', 'date'));
    }

    /**
     * Actualiza una fecha (shallow).
     * Ruta: PUT/PATCH admin/dates/{date}
     */
    public function update(Request $r, $id)
    {
        $date = TourDate::withTrashed()->findOrFail($id);

        $data = $r->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'capacity'   => 'required|integer|min:1',
            'available'  => 'required|integer|min:0',
            'price'      => 'required|numeric|min:0',
            'is_active'  => 'boolean',
        ]);
        $data['is_active'] = $r->boolean('is_active');

        if ($data['available'] > $data['capacity']) {
            return back()->withInput()->withErrors([
                'available' => 'Disponible no puede superar la capacidad.',
            ]);
        }

        $date->update($data);

        return redirect()->route(
            'admin.tours.edit',
            array_merge(['tour' => $date->tour_id], $this->backQuery($r))
        )->with('ok', 'Fecha actualizada.');
    }

    /**
     * Soft delete (papelera) de una fecha (shallow).
     * Ruta: DELETE admin/dates/{date}
     */
    public function destroy(Request $r, $id)
    {
        $date   = TourDate::findOrFail($id);
        $tourId = $date->tour_id;

        $date->delete(); // soft delete

        return redirect()->route(
            'admin.tours.edit',
            array_merge(['tour' => $tourId], $this->backQuery($r))
        )->with('ok', 'Fecha enviada a papelera.');
    }

    /**
     * Restaurar fecha desde papelera (custom).
     * Ruta: POST admin/dates/{id}/restore
     */
    public function restore(Request $r, $id)
    {
        $date = TourDate::withTrashed()->findOrFail($id);

        if (! $date->trashed()) {
            return back()->with('error', 'La fecha no está en papelera.');
        }

        $date->restore();

        return back()->with('ok', 'Fecha restaurada.');
    }

    /**
     * Eliminar definitivamente (hard delete) una fecha (custom).
     * Ruta: DELETE admin/dates/{id}/force
     */
    public function forceDelete(Request $r, $id)
    {
        $date   = TourDate::withTrashed()->findOrFail($id);
        $tourId = $date->tour_id;

        // ⚠️ Esto borra definitivamente la fecha.
        // Si hay FKs en cascada, también eliminará dependencias asociadas.
        $date->forceDelete();

        return redirect()->route(
            'admin.tours.edit',
            array_merge(['tour' => $tourId], $this->backQuery($r))
        )->with('ok', 'Fecha eliminada definitivamente.');
    }
}
