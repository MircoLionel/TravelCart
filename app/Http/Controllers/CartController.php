<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Tour;
use App\Models\TourDate;
use Illuminate\Http\Request;

class CartController extends Controller
{
    private function getOrCreateCart($user): Cart
    {
        return Cart::firstOrCreate(['user_id'=>$user->id,'status'=>'pending']);
    }

    public function show(Request $request)
    {
        $cart = Cart::where('user_id',$request->user()->id)
            ->where('status','pending')
            ->with(['items.tour','items.tourDate'])
            ->first();

        return view('cart.show', compact('cart'));
    }

    public function add(Request $request)
    {
        $data = $request->validate([
            'tour_id'      => ['required','exists:tours,id'],
            'tour_date_id' => ['required','exists:tour_dates,id'],
            'qty'          => ['required','integer','min:1','max:10'],
        ]);

        $tour     = Tour::findOrFail($data['tour_id']);
        $tourDate = TourDate::findOrFail($data['tour_date_id']);

        if (!$tour->is_active || !$tourDate->is_active) return back()->with('error','Tour o fecha inactiva.');
        if ($tourDate->start_date->lt(now()->startOfDay())) return back()->with('error','La salida ya pasó.');
        if ($tourDate->available < $data['qty']) return back()->with('error','No hay cupos suficientes.');

        $cart = $this->getOrCreateCart($request->user());

        $item = CartItem::firstOrNew([
            'cart_id'=>$cart->id,'tour_id'=>$tour->id,'tour_date_id'=>$tourDate->id,
        ]);
        $item->qty = ($item->exists ? $item->qty : 0) + (int)$data['qty'];
        if ($item->qty > $tourDate->available) return back()->with('error','No hay cupos suficientes para esa cantidad.');

        $item->unit_price = (float) $tourDate->price;
        $item->subtotal   = $item->unit_price * $item->qty;
        $item->save();

        return redirect()->route('cart.show')->with('ok','Agregado al carrito.');
    }

    public function remove(Request $request, CartItem $item)
    {
        $cart = $this->getOrCreateCart($request->user());
        abort_unless($item->cart_id === $cart->id, 403);
        $item->delete();
        return back()->with('ok','Ítem eliminado.');
    }
}
