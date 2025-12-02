<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reservation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_id','tour_id','tour_date_id','vendor_id','qty','status','locator','hold_expires_at','total_amount'
    ]; // status: pending|confirmed|cancelled

    protected $casts = [
        'hold_expires_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }

    public function tourDate(): BelongsTo
    {
        return $this->belongsTo(TourDate::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function passengers()
    {
        return $this->hasMany(ReservationPassenger::class);
    }

    public function payments()
    {
        return $this->hasMany(ReservationPayment::class);
    }

    public function paidAmount(): int
    {
        return (int) $this->payments()->sum('amount');
    }

    public function paidPercentage(): int
    {
        if (!$this->total_amount) {
            return 0;
        }

        return (int) min(100, round(($this->paidAmount() / $this->total_amount) * 100));
    }

    public function vendorCommissionRate(): float
    {
        return 0.13;
    }

    public function vendorCommissionAmount(): int
    {
        if (!$this->total_amount) {
            return 0;
        }

        return (int) round($this->total_amount * $this->vendorCommissionRate());
    }

    public function providerNetAmount(): int
    {
        return max(0, (int) $this->total_amount - $this->vendorCommissionAmount());
    }

    public function outstandingAmount(): int
    {
        return max(0, (int) $this->providerNetAmount() - $this->paidAmount());
    }
}
