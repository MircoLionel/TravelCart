<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Audit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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

    public function update(Request $request, User $vendor)
    {
        if (!$vendor->isVendor()) {
            abort(404);
        }

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
}
