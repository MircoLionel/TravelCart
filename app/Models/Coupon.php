<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Coupon extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code','type','amount','min_total','role_only',
        'max_uses','max_uses_per_user','starts_at','ends_at','is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
    ];

    public function redemptions() {
        return $this->hasMany(CouponRedemption::class);
    }

    public function isValidForUser(?User $user, int $subtotal): bool
    {
        if (!$this->is_active) return false;
        $now = Carbon::now();
        if ($this->starts_at && $now->lt($this->starts_at)) return false;
        if ($this->ends_at && $now->gt($this->ends_at)) return false;
        if ($subtotal < (int)$this->min_total) return false;

        if ($this->role_only && $user) {
            if (($user->role ?? null) !== $this->role_only) return false;
        }

        // max uses global
        if ($this->max_uses !== null) {
            $used = $this->redemptions()->count();
            if ($used >= $this->max_uses) return false;
        }

        // max por user
        if ($user && $this->max_uses_per_user !== null) {
            $usedByUser = $this->redemptions()->where('user_id', $user->id)->count();
            if ($usedByUser >= $this->max_uses_per_user) return false;
        }

        return true;
    }

    public function discountFor(int $subtotal): int
    {
        if ($subtotal <= 0) return 0;
        if ($this->type === 'percent') {
            $pct = max(0, min(100, (int)$this->amount));
            return (int) floor($subtotal * $pct / 100);
        }
        // fixed
        return (int) min((int)$this->amount, $subtotal);
    }
}
