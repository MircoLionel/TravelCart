<?php
namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Reservation;
use App\Models\TourDate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function show(Request $request)
    {
        $cart = Cart::where('user_id',$request->user()->id)
            ->where('status','pending')
            ->with(['items.tour','items.tourDate'])
            ->first();

        abort_if(!$cart || $cart->items->isEmpty(), 404);

        return view('checkout.show', compact('cart'));
    }

    public function placeOrder(Request $request)
    {
        $user = $request->user();

        $cart = Cart::where('user_id',$user->id)
            ->where('status','pending')
            ->with(['items.tour','items.tourDate'])
            ->firstOrFail();

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.show')->with('error','Tu carrito está vacío.');
        }

        $order = null;

        DB::transaction(function () use ($cart, $user, &$order) {
            // 1) Revalidar disponibilidad con LOCK para no sobre-reservar
            foreach ($cart->items as $it) {
                // bloqueamos la fila de tour_dates
                $td = TourDate::where('id', $it->tour_date_id)->lockForUpdate()->first();
                if (!$td->is_active || $td->start_date->lt(now()->startOfDay())) {
                    throw new \Exception('Fecha inactiva o vencida.');
                }
                if ($td->available < $it->qty) {
                    throw new \Exception('Sin cupo suficiente.');
                }
            }

            // 2) Crear orden
            $total = (float) $cart->items->sum('subtotal');
            $order = Order::create([
                'user_id' => $user->id,
                'code'    => Str::upper(Str::random(8)),
                'status'  => 'paid', // simulamos pago aprobado
                'total'   => $total,
            ]);

            // 3) Crear order items y descontar cupo
            foreach ($cart->items as $it) {
                OrderItem::create([
                    'order_id'     => $order->id,
                    'tour_id'      => $it->tour_id,
                    'tour_date_id' => $it->tour_date_id,
                    'qty'          => $it->qty,
                    'unit_price'   => $it->unit_price,
                    'subtotal'     => $it->subtotal,
                ]);

                TourDate::where('id',$it->tour_date_id)
                    ->decrement('available', $it->qty);
            }

            // 4) Crear reserva (única, agregada)
            $first = $cart->items->first();
            Reservation::create([
                'order_id'     => $order->id,
                'tour_id'      => $first->tour_id,
                'tour_date_id' => $first->tour_date_id,
                'qty'          => $cart->items->sum('qty'),
                'status'       => 'confirmed',
                'locator'      => 'RSV-'.now()->format('ymd').'-'.Str::upper(Str::random(5)),
            ]);

            // 5) Cerrar carrito
            $cart->update(['status'=>'converted']);
        });

        return redirect()->route('orders.show', $order)->with('ok','¡Reserva confirmada!');
    }
}