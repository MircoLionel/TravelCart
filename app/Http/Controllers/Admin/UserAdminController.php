<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserAdminController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q')->toString();

        $users = User::query()
            ->when($q, fn($qb) =>
                $qb->where(function($qq) use ($q) {
                    $qq->where('name', 'like', "%{$q}%")
                       ->orWhere('email', 'like', "%{$q}%")
                       ->orWhere('legajo', 'like', "%{$q}%");
                })
            )
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'q'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'role'    => ['required', Rule::in(['buyer', 'supplier'])],
            'legajo'  => ['nullable', 'string', 'max:50'],
            'is_admin'=> ['nullable', 'boolean'],
        ]);

        // Evitar que un admin se quite a sí mismo el admin sin querer
        if ($request->user()->is($user) && empty($data['is_admin'])) {
            return back()->with('error', 'No podés quitarte el rol de admin a vos mismo.');
        }

        $user->role     = $data['role'];
        $user->legajo   = $data['legajo'] ?: null;
        $user->is_admin = (bool)($data['is_admin'] ?? false);
        $user->save();

        return back()->with('ok', 'Usuario actualizado.');
    }

    public function approve(User $user)
    {
        if (!$user->legajo) {
            return back()->with('error', 'Para aprobar, primero cargá un legajo.');
        }

        $user->is_approved = true;
        $user->save();

        return back()->with('ok', 'Usuario aprobado.');
    }

    public function revoke(User $user)
    {
        // Ojo: no bloqueamos al propio admin que hace la acción
        if (auth()->id() === $user->id) {
            return back()->with('error', 'No podés revocar tu propia aprobación.');
        }

        $user->is_approved = false;
        $user->save();

        return back()->with('ok', 'Aprobación revocada.');
    }
}
