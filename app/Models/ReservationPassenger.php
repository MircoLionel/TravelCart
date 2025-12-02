<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReservationPassenger extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id', 'document_number', 'first_name', 'last_name', 'birth_date', 'sex',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }
}
