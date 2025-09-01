<?php

namespace App\Models;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Campos asignables en masa.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'legajo',
        'role',         // 'admin' | 'vendor' | 'buyer'
        'is_approved',  // bool
        'is_admin',     // bool (opcional; convive con role)
    ];

    /**
     * Campos ocultos en serializaciones.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casts de atributos.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_approved'       => 'bool',
        'is_admin'          => 'bool',
        'password'          => 'hashed', // hash automático al asignar
    ];

    /**
     * Helpers de rol.
     */
    public function isAdmin(): bool
    {
        return ($this->is_admin ?? false) || $this->role === 'admin';
    }

    public function isVendor(): bool
    {
        return $this->role === 'vendor';
    }

    public function isBuyer(): bool
    {
        // Considera buyer por defecto si no hay role
        return $this->role === 'buyer' || is_null($this->role);
    }

    /**
     * Scopes útiles.
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopeAdmins($query)
    {
        return $query->where(function ($q) {
            $q->where('is_admin', true)
              ->orWhere('role', 'admin');
        });
    }

    /**
     * Relaciones.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
