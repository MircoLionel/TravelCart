<?php

namespace App\Http\Controllers;

use App\Mail\OrderConfirmed;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\TourDate;
use App\Models\CouponRedemption;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
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
                'cart'  => null,
                'error' => 'Tu carrito está vacío.',
            ]);
        }

        // Subtotal actual del carrito
        $subtotal = (int) $cart->items->sum('subtotal');

        // Descuento guardado en sesión (si aplicaste un cupón)
        $discount = (int) session('cart.discount', 0);
        if ($discount > $subtotal) {
            $discount = $subtotal; // clamp
        }

        $total = max(0, $subtotal - $discount);

        return view('checkout.show', compact('cart', 'subtotal', 'discount', 'total'));
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

        try {
            return DB::transaction(function () use ($request, $user, $cart) {
                $lockedDates = [];
                $requestedPerDate = [];

                foreach ($cart->items as $ci) {
                    $tourDateId = $ci->tour_date_id ?? $ci->date_id;
                    if ($tourDateId) {
                        $requestedPerDate[$tourDateId] = ($requestedPerDate[$tourDateId] ?? 0) + (int) $ci->qty;
                    }
                }

                foreach ($requestedPerDate as $tourDateId => $quantity) {
                    $tourDate = TourDate::query()
                        ->whereKey($tourDateId)
                        ->lockForUpdate()
                        ->first();

                    if (!$tourDate) {
                        throw new \RuntimeException('La fecha seleccionada ya no está disponible.');
                    }

                    $tourDate->loadMissing('tour');

                    if ($tourDate->available < $quantity) {
                        $title = $tourDate->tour?->title ?? 'el tour seleccionado';
                        $dateLabel = optional($tourDate->start_date)->format('d/m/Y');
                        $remaining = max(0, (int) $tourDate->available);

                        throw new \RuntimeException(
                            "No hay suficientes cupos disponibles para {$title} en la fecha {$dateLabel}. Solo quedan {$remaining}."
                        );
                    }

                    $lockedDates[$tourDateId] = $tourDate;
                }

                // Subtotal y descuento de sesión (si existiera)
                $subtotal = (int) $cart->items->sum('subtotal');
                $discount = (int) session('cart.discount', 0);
                if ($discount > $subtotal) {
                    $discount = $subtotal; // clamp
                }
                $total = max(0, $subtotal - $discount);

                // 1) Crear la orden
                $order = new Order();
                $order->user_id            = $user->id;
                $order->code               = $this->generateCode();
                $order->status             = 'paid'; // simulamos pago OK
                $order->total              = $total;
                $order->discount_total     = $discount;
                $order->applied_coupon_code= session('cart.coupon_code');
                $order->save();

                // 2) Items de la orden
                foreach ($cart->items as $ci) {
                    $oi = new OrderItem();
                    $oi->order_id     = $order->id;
                    $oi->tour_id      = $ci->tour_id;
                    $oi->tour_date_id = $ci->tour_date_id ?? $ci->date_id; // compatibilidad de columnas
                    $oi->qty          = (int) $ci->qty;
                    $oi->unit_price   = (int) $ci->unit_price;
                    $oi->subtotal     = (int) $ci->subtotal;
                    $oi->save();

                    $tourDateId = $ci->tour_date_id ?? $ci->date_id;
                    if ($tourDateId && isset($lockedDates[$tourDateId])) {
                        $lockedDates[$tourDateId]->decrement('available', (int) $ci->qty);
                    }
                }

                // 3) Registrar redención de cupón (si aplica)
                if (session()->has('cart.coupon_id') && $discount > 0) {
                    CouponRedemption::create([
                        'coupon_id' => (int) session('cart.coupon_id'),
                        'user_id'   => $user->id,
                        'order_id'  => $order->id,
                        'discount'  => $discount,
                    ]);
                }

                // 4) Cerrar carrito
                try {
                    $cart->status = 'closed';
                    $cart->save();
                } catch (QueryException $e) {
                    $cart->status = 'converted';
                    $cart->save();
                }

                // 5) Limpiar datos de cupón en sesión
                session()->forget(['cart.coupon_id','cart.coupon_code','cart.discount']);

                // 6) Enviar correo de confirmación (HTML + voucher si lo agregaste)
                Mail::to($user->email)->send(
                    new OrderConfirmed($order->load(['items.tour', 'items.tourDate', 'user']))
                );

                // 7) Redirigir a pantalla de confirmación de orden
                return redirect()->route('orders.show', $order);
            });
        } catch (\RuntimeException $e) {
            return redirect()->route('cart.show')->with('error', $e->getMessage());
        }
    }

    private function generateCode(): string
    {
        return strtoupper(str()->random(7));
    }
}
