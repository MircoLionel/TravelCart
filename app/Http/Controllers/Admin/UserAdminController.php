<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserAdminController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $users = User::query()
            ->when($q, function ($query) use ($q) {
                $query->where(function ($qB) use ($q) {
                    $qB->where('name', 'like', "%{$q}%")
                       ->orWhere('email', 'like', "%{$q}%")
                       ->orWhere('legajo', 'like', "%{$q}%");
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'q'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'role'        => 'required|in:admin,vendor,buyer',
            'is_approved' => 'required|boolean',
            'legajo'      => 'nullable|string|max:50',
        ]);

        // Mantener is_admin si lo usás para compatibilidad
        $validated['is_admin'] = $validated['role'] === 'admin';

        $user->update($validated);

        return back()->with('status', 'Usuario actualizado correctamente.');
    }
}
