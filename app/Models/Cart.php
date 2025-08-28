<?php

namespace App\Models;

use App\Models\CartItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'status'];

    /** Relación: items del carrito */
    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Devuelve el carrito OPEN del usuario.
     * - Si hay más de uno, prioriza el que tiene más items (o el más nuevo).
     * - Si no hay ninguno, crea uno nuevo.
     */
    public static function forUserOpen(User $user): Cart
    {
        $cart = static::withCount('items')
            ->where('user_id', $user->id)
            ->where('status', 'open')
            ->orderByDesc('items_count')
            ->orderByDesc('id')
            ->first();

        if (! $cart) {
            $cart = static::create([
                'user_id' => $user->id,
                'status'  => 'open',
            ]);
        }

        return $cart;
    }

    /** Total calculado del carrito (fallback si subtotal viniera null) */
    public function getTotalAttribute(): int
    {
        return (int) $this->items->sum(function ($i) {
            $sub = $i->subtotal ?? ($i->qty * $i->unit_price);
            return (int) $sub;
        });
    }
}
