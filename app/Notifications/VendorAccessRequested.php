<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class VendorAccessRequested extends Notification
{
    use Queueable;

    public function __construct(public User $buyer)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => "Nuevo comprador solicita acceso: {$this->buyer->name} ({$this->buyer->email})",
        ];
    }
}
