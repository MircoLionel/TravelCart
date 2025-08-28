<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    /** GET /checkout */
    public function show(Request $request)
    {
        $cart = Cart::forUserOpen($request->user())
            ->load(['items.tour', 'items.tourDate']);

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.show')->with('error', 'Tu carrito está vacío.');
        }

        return view('checkout.show', compact('cart'));
    }

    /** POST /checkout */
    public function placeOrder(Request $request)
    {
        $cart = Cart::forUserOpen($request->user())->load('items');

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.show')->with('error', 'Tu carrito está vacío.');
        }

        // Acá iría tu lógica real de creación de orden.
        // Para esta etapa, sólo simulo el cierre del carrito.
        DB::transaction(function () use ($cart) {
            $cart->update(['status' => 'closed']);
        });

        return redirect()->route('orders.show', ['order' => 1])
            ->with('ok', '¡Orden creada! (simulada)');
    }
}
