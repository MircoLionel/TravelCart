<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\TourDate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CartController extends Controller
{
    /** Mostrar /cart */
    public function show(Request $request)
    {
        $cart = Cart::forUserOpen($request->user())
            ->load(['items.tour', 'items.tourDate']);

        return view('cart.show', compact('cart'));
    }

    /** Agregar item al carrito */
    public function add(Request $request)
    {
        // Acepta date_id O tour_date_id (cualquiera de los dos)
        $data = $request->validate([
            'tour_id'      => ['required','integer','exists:tours,id'],
            'date_id'      => ['required_without:tour_date_id','integer','exists:tour_dates,id'],
            'tour_date_id' => ['required_without:date_id','integer','exists:tour_dates,id'],
            'qty'          => ['required','integer','min:1'],
        ], [
            'date_id.required_without'      => 'Falta la fecha.',
            'tour_date_id.required_without' => 'Falta la fecha.',
        ]);

        // Unificamos el ID de la fecha
        $dateId = (int) ($data['date_id'] ?? $data['tour_date_id']);

        // Detectar cómo se llama la FK en cart_items
        $dateCol = Schema::hasColumn('cart_items', 'tour_date_id') ? 'tour_date_id' : 'date_id';

        $cart = Cart::forUserOpen($request->user());

        return DB::transaction(function () use ($cart, $data, $dateId, $dateCol) {

            /** @var \App\Models\TourDate $date */
            $date = TourDate::whereKey($dateId)->lockForUpdate()->firstOrFail();

            if (! $date->is_active) {
                return back()->with('error','La fecha no está activa.');
            }
            if ($date->start_date->isPast()) {
                return back()->with('error','La salida ya pasó.');
            }

            // Si ya existe mismo tour+fecha, acumulamos cantidad
            $existing = CartItem::where('cart_id', $cart->id)
                ->where('tour_id', (int) $data['tour_id'])
                ->where($dateCol, $dateId)
                ->first();

            $newQty = ($existing?->qty ?? 0) + (int) $data['qty'];

            if ($newQty > $date->available) {
                return back()->with('error', 'No hay cupos suficientes para esa cantidad.');
            }

            $item = $existing ?? new CartItem([
                'cart_id' => $cart->id,
                'tour_id' => (int) $data['tour_id'],
                $dateCol  => $dateId,
            ]);

            $item->qty        = $newQty;
            $item->unit_price = $date->price;
            $item->subtotal   = $item->qty * $item->unit_price;
            $item->save();

            return redirect()->route('cart.show')->with('ok','Agregado al carrito.');
        });
    }

    /** Quitar item del carrito */
    public function remove(Request $request, CartItem $item)
    {
        $cart = Cart::forUserOpen($request->user());

        if ((int) $item->cart_id !== (int) $cart->id) {
            abort(403);
        }

        $item->delete();

        return back()->with('ok', 'Item eliminado.');
    }
}
