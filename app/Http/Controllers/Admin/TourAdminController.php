<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use Illuminate\Http\Request;

class TourAdminController extends Controller
{
    /**
     * Devuelve los parámetros de consulta que queremos preservar
     * (status y q) para reusarlos en redirects/links.
     */
    private function backQuery(Request $r): array
    {
        return $r->only('status', 'q');
    }

    /**
     * Listado con pestañas (Todos/Activos/Papelera) y búsqueda.
     */
    public function index(Request $r)
    {
        $status = $r->query('status', 'all');   // all | active | trashed
        $q      = trim($r->query('q', ''));     // búsqueda por título/destino

        $query = Tour::query();

        // Búsqueda
        if ($q !== '') {
            $query->where(function ($qq) use ($q) {
                $like = '%' . str_replace('%', '\%', $q) . '%';
                $qq->where('title', 'like', $like)
                   ->orWhere('destination', 'like', $like);
            });
        }

        // Filtro por estado
        switch ($status) {
            case 'active':
                $query->whereNull('deleted_at');  // solo activos
                break;
            case 'trashed':
                $query->onlyTrashed();            // solo papelera
                break;
            default:
                $query->withTrashed();            // todos
                $status = 'all';
                break;
        }

        $tours = $query->orderByDesc('id')
                       ->paginate(12)
                       ->appends($r->only('status', 'q')); // conservar filtros

        // Contadores (totales por estado, sin aplicar búsqueda)
        $counts = [
            'all'     => Tour::withTrashed()->count(),
            'active'  => Tour::count(),
            'trashed' => Tour::onlyTrashed()->count(),
        ];

        return view('admin.tours.index', compact('tours', 'status', 'counts', 'q'));
    }

    public function create(Request $r)
    {
        // Por si querés usar status/q en la vista de creación
        $status = $r->query('status', 'all');
        $q      = trim($r->query('q', ''));
        return view('admin.tours.create', compact('status', 'q'));
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'base_price'  => 'required|numeric|min:0',
            'days'        => 'required|integer|min:1',
            'origin'      => 'nullable|string|max:255',
            'destination' => 'required|string|max:255',
            'is_active'   => 'boolean',
        ]);
        $data['is_active'] = $r->boolean('is_active');

        $tour = Tour::create($data);

        return redirect()->route(
            'admin.tours.edit',
            array_merge(['tour' => $tour->id], $this->backQuery($r))
        )->with('ok', 'Tour creado.');
    }

    public function edit(Request $r, $id)
    {
        // Permitir ver/editar incluso si está en papelera (para restaurarlo)
        $tour = Tour::withTrashed()
            ->with(['dates' => fn ($q) => $q->withTrashed()->orderBy('start_date')])
            ->findOrFail($id);

        return view('admin.tours.edit', compact('tour'));
    }

    public function update(Request $r, $id)
    {
        $tour = Tour::withTrashed()->findOrFail($id);

        $data = $r->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'base_price'  => 'required|numeric|min:0',
            'days'        => 'required|integer|min:1',
            'origin'      => 'nullable|string|max:255',
            'destination' => 'required|string|max:255',
            'is_active'   => 'boolean',
        ]);
        $data['is_active'] = $r->boolean('is_active');

        $tour->update($data);

        return back()->with('ok', 'Tour actualizado.');
    }

    /**
     * Soft delete → envía a papelera.
     */
    public function destroy(Request $r, $id)
    {
        $tour = Tour::findOrFail($id);
        $tour->delete();

        return redirect()->route('admin.tours.index', $this->backQuery($r))
            ->with('ok', 'Tour enviado a papelera.');
    }

    /**
     * Restaurar desde papelera.
     */
    public function restore(Request $r, $id)
    {
        $tour = Tour::withTrashed()->findOrFail($id);

        if (! $tour->trashed()) {
            return back()->with('error', 'El tour no está en papelera.');
        }

        $tour->restore();

        return back()->with('ok', 'Tour restaurado.');
    }

    /**
     * Eliminar definitivamente (hard delete).
     * ¡Cuidado! Si tus FKs tienen cascade, también borra fechas asociadas.
     */
    public function forceDelete(Request $r, $id)
    {
        $tour = Tour::withTrashed()->findOrFail($id);
        $tour->forceDelete();

        return redirect()->route('admin.tours.index', $this->backQuery($r))
            ->with('ok', 'Tour eliminado definitivamente.');
    }
}
