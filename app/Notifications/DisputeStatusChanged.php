<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

final class DisputeStatusChanged extends Notification
{
    use Queueable;

    public function __construct(
        public int $disputeId,
        public int $orderId,
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
            'dispute_id' => $this->disputeId,
            'order_id' => $this->orderId,
            'status' => $this->newStatus,
            'message' => $this->message,
        ];
    }
}
