<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Audit;
use App\Models\ReservationPassenger;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class VendorAdminController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $vendors = User::query()
            ->where('role', 'vendor')
            ->when($q, function ($query) use ($q) {
                $query->where(function ($inner) use ($q) {
                    $inner->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('legajo', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.vendors.index', compact('vendors', 'q'));
    }

    public function show(User $vendor)
    {
        $this->ensureVendor($vendor);

        $tours = $vendor->vendorTours()
            ->withCount(['reservations as sales_count' => function ($q) {
                $q->whereNull('reservations.deleted_at');
            }])
            ->withSum(['reservations as sales_amount' => function ($q) {
                $q->whereNull('reservations.deleted_at');
            }], 'total_amount')
            ->orderBy('title')
            ->get();

        $maxCount = max(1, (int) $tours->max('sales_count'));
        $maxAmount = max(1, (int) $tours->max('sales_amount'));

        return view('admin.vendors.show', compact('vendor', 'tours', 'maxCount', 'maxAmount'));
    }

    public function update(Request $request, User $vendor)
    {
        $this->ensureVendor($vendor);

        $validated = $request->validate([
            'is_approved' => ['required', 'boolean'],
            'legajo'      => ['nullable', 'string', 'max:50'],
            'name'        => ['nullable', 'string', 'max:255'],
            'email'       => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($vendor->id)],
        ]);

        $payload = [
            'role'        => 'vendor',
            'is_admin'    => false,
            'is_approved' => $request->boolean('is_approved'),
            'legajo'      => $validated['legajo'] ?? null,
        ];

        if (!empty($validated['name'])) {
            $payload['name'] = $validated['name'];
        }

        if (!empty($validated['email'])) {
            $payload['email'] = $validated['email'];
        }

        $vendor->fill($payload)->save();

        Audit::create([
            'actor_id'    => $request->user()->id,
            'action'      => 'vendor_updated',
            'target_type' => User::class,
            'target_id'   => $vendor->id,
            'meta'        => $payload,
        ]);

        return back()->with('status', 'Proveedor actualizado.');
    }

    public function exportPassengers(User $vendor)
    {
        $this->ensureVendor($vendor);

        $passengers = ReservationPassenger::query()
            ->selectRaw('tours.title as tour_title, tour_dates.start_date, reservations.locator, reservation_passengers.first_name, reservation_passengers.last_name, reservation_passengers.document_number, reservation_passengers.birth_date, reservation_passengers.sex, users.name as buyer_name, users.email as buyer_email')
            ->join('reservations', 'reservations.id', '=', 'reservation_passengers.reservation_id')
            ->leftJoin('tours', 'tours.id', '=', 'reservations.tour_id')
            ->leftJoin('tour_dates', 'tour_dates.id', '=', 'reservations.tour_date_id')
            ->leftJoin('orders', 'orders.id', '=', 'reservations.order_id')
            ->leftJoin('users', 'users.id', '=', 'orders.user_id')
            ->where('reservations.vendor_id', $vendor->id)
            ->whereNull('reservations.deleted_at')
            ->orderBy('tour_dates.start_date')
            ->orderBy('tours.title')
            ->orderBy('reservation_passengers.last_name')
            ->get();

        $lines = [
            'Tour\tFecha salida\tLocalizador\tPasajero\tDocumento\tNacimiento\tSexo\tComprador\tEmail comprador',
        ];

        foreach ($passengers as $passenger) {
            $startDate = $passenger->start_date ? Carbon::parse($passenger->start_date)->format('Y-m-d') : '';
            $birthDate = $passenger->birth_date ? Carbon::parse($passenger->birth_date)->format('Y-m-d') : '';

            $lines[] = collect([
                $passenger->tour_title,
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
        $filename = 'pasajeros_proveedor_' . Str::slug($vendor->name ?: 'proveedor') . '.xls';

        return response($content)
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    private function ensureVendor(User $user): void
    {
        if (!$user->isVendor()) {
            throw new NotFoundHttpException();
        }
    }
}
