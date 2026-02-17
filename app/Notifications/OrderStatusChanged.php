<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

final class OrderStatusChanged extends Notification
{
    use Queueable;

    public function __construct(
        public int $orderId,
        public string $orderCode,
        public string $newStatus,
        public string $message
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'order_id' => $this->orderId,
            'order_code' => $this->orderCode,
            'status' => $this->newStatus,
            'message' => $this->message,
        ];
    }
}
