<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tour extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title','description','base_price','days','origin','destination','is_active'
    ];

    public function dates(): HasMany
    {
        return $this->hasMany(TourDate::class);
    }
}
