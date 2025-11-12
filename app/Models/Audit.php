<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Audit extends Model
{
    protected $fillable = ['actor_id','action','target_type','target_id','meta'];

    protected $casts = [
        'meta' => 'array',
    ];

    public function actor() {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
