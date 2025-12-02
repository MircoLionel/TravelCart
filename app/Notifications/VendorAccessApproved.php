<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class VendorAccessApproved extends Notification
{
    use Queueable;

    public function __construct(public User $vendor)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => "Tu proveedor {$this->vendor->name} aprobó tu acceso.",
        ];
    }
}
