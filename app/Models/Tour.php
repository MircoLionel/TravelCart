<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Reservation;

class Tour extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title','description','base_price','days','origin','destination','is_active','vendor_id'
    ];

    public function dates(): HasMany
    {
        return $this->hasMany(TourDate::class);
    }

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }
}
