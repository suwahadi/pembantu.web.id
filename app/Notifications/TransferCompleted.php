<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

final class TransferCompleted extends Notification
{
    use Queueable;

    public function __construct(
        public string $type,
        public int $transferId,
        public ?int $orderId,
        public string $message,
        public int $amountIdr
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => $this->type,
            'transfer_id' => $this->transferId,
            'order_id' => $this->orderId,
            'amount_idr' => $this->amountIdr,
            'message' => $this->message,
        ];
    }
}
