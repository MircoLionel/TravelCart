<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Audit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserAdminController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $pendingCount = User::query()
            ->where(function ($query) {
                $query->whereNull('role')
                    ->orWhere('is_approved', false);
            })
            ->count();

        $users = User::query()
            ->when($q, function ($query) use ($q) {
                $query->where(function ($qB) use ($q) {
                    $qB->where('name', 'like', "%{$q}%")
                       ->orWhere('email', 'like', "%{$q}%")
                       ->orWhere('legajo', 'like', "%{$q}%");
                });
            })
            ->orderByRaw('CASE WHEN role IS NULL THEN 0 ELSE 1 END')
            ->orderBy('is_approved')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'q', 'pendingCount'));
    }

    // Opcional: si tenés vista de edición individual
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        // 1) Validación de los campos que el admin puede cambiar
        $validated = $request->validate([
            'role'        => ['required', Rule::in(['admin','vendor','buyer'])],
            'is_approved' => ['sometimes', 'boolean'],
            'legajo'      => ['nullable', 'string', 'max:50'],
            // Si también querés permitir modificar nombre/email, descomentá:
            // 'name'   => ['sometimes','string','max:255'],
            // 'email'  => ['sometimes','email','max:255', Rule::unique('users','email')->ignore($user->id)],
        ]);

        // Normalizamos boolean (por si viene 'on'/'1')
        $validated['is_approved'] = $request->boolean('is_approved');

        // 2) Mantener compatibilidad con is_admin (si lo seguís usando)
        $validated['is_admin'] = ($validated['role'] === 'admin');

        // 3) Actualizamos el usuario
        $user->fill($validated)->save();

        // 4) Registramos AUDITORÍA (acá va la implementación que querías)
        Audit::create([
            'actor_id'    => $request->user()->id,        // quién hizo el cambio
            'action'      => 'user_updated',               // etiqueta de acción
            'target_type' => User::class,                  // el tipo de objeto afectado
            'target_id'   => $user->id,                    // el ID del usuario afectado
            'meta'        => [
                'role'        => $validated['role'],
                'is_approved' => $validated['is_approved'],
                'legajo'      => $validated['legajo'] ?? null,
                // si habilitás name/email en validación arriba, podés loguearlos también:
                // 'name'     => $validated['name']  ?? $user->name,
                // 'email'    => $validated['email'] ?? $user->email,
            ],
        ]);

        return back()->with('status', 'Usuario actualizado correctamente.');
    }
}
