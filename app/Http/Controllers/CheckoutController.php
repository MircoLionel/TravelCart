<?php

namespace App\Http\Controllers;

use App\Mail\OrderConfirmed;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class CheckoutController extends Controller
{
    /**
     * Pantalla de checkout (resumen del carrito).
     */
    public function show(Request $request)
    {
        $cart = Cart::forUserOpen($request->user())
            ->load(['items.tour', 'items.tourDate']);

        if (!$cart || $cart->items->isEmpty()) {
            return view('checkout.show', [
                'cart' => null,
                'error' => 'Tu carrito está vacío.',
            ]);
        }

        $total = (float) $cart->items->sum('subtotal');

        return view('checkout.show', compact('cart', 'total'));
    }
    

    /**
     * Confirma la compra, crea la orden, cierra carrito y envía correo.
     */
    public function placeOrder(Request $request)
    {
        $user = $request->user();

        $cart = Cart::forUserOpen($user)->load(['items.tour', 'items.tourDate']);
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.show')->with('error', 'Tu carrito está vacío.');
        }

        return DB::transaction(function () use ($user, $cart) {

            // 1) Crear la orden
            $order = new Order();
            $order->user_id = $user->id;
            $order->code    = $this->generateCode();
            $order->status  = 'paid'; // simulamos pago OK
            $order->total   = (float) $cart->items->sum('subtotal');
            $order->save();

            // 2) Items de la orden
            foreach ($cart->items as $ci) {
                $oi = new OrderItem();
                $oi->order_id    = $order->id;
                $oi->tour_id     = $ci->tour_id;
                $oi->tour_date_id= $ci->tour_date_id ?? $ci->date_id; // por si tu columna se llama distinto
                $oi->qty         = $ci->qty;
                $oi->unit_price  = $ci->unit_price;
                $oi->subtotal    = $ci->subtotal;
                $oi->save();
            }

            // 3) Cerrar carrito
            $cart->status = 'closed';
            $cart->save();

            // 4) Enviar correo de confirmación
            Mail::to($user->email)->send(new OrderConfirmed($order->load(['items.tour', 'items.tourDate', 'user'])));

            // 5) Redirigir a pantalla de confirmación de orden
            return redirect()->route('orders.show', $order);
        });
    }

    private function generateCode(): string
    {
        return strtoupper(str()->random(7));
    }
}
