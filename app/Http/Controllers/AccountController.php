<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AccountController extends Controller
{
    /**
     * Pantalla para usuarios NO aprobados.
     * Solo requiere estar autenticado; NO debe llevar el middleware "approved".
     */
    public function pending(Request $request)
    {
        // Si ya fue aprobado, lo mandamos al dashboard o a donde prefieras
        if ($request->user()?->is_approved) {
            return redirect()->route('dashboard');
        }

        return view('account.pending');
    }
}
