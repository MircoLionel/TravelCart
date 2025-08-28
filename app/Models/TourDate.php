<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TourDate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tour_id','start_date','end_date','capacity','available','price','is_active'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'is_active'  => 'boolean',
    ];

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }
}
