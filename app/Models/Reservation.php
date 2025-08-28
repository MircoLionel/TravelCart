<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_id','tour_id','tour_date_id','qty','status','locator'
    ]; // status: pending|confirmed|cancelled

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
}
