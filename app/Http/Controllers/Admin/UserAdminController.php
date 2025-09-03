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
        $q = trim((string)$request->query('q',''));

        $users = User::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($q2) use ($q) {
                    $q2->where('name','like',"%{$q}%")
                       ->orWhere('email','like',"%{$q}%")
                       ->orWhere('legajo','like',"%{$q}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', compact('users','q'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'role'        => ['required', Rule::in(['admin','vendor','buyer'])],
            'is_approved' => ['sometimes','boolean'],
            'legajo'      => ['nullable','string','max:50'],
        ]);

        $data['is_approved'] = (bool)($data['is_approved'] ?? false);

        $user->fill($data)->save();

        return redirect()
            ->route('admin.users.edit', $user)
            ->with('ok','Usuario actualizado correctamente.');
    }
}
