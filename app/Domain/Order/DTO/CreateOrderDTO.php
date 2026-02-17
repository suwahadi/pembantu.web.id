<?php

namespace App\Domain\Order\DTO;

class CreateOrderDTO
{
    public function __construct(
        public int $visitorUserId,
        public int $workerId,
        public int $categoryId,
        public string $startDate,
        public ?string $endDate = null,
        public int $subtotalIdr = 0,
        public int $platformFeeIdr = 0,
        public int $totalIdr = 0,
        public ?string $notes = null,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            visitorUserId: (int) $data['visitor_user_id'],
            workerId: (int) $data['worker_id'],
            categoryId: (int) $data['category_id'],
            startDate: $data['start_date'],
            endDate: $data['end_date'] ?? null,
            subtotalIdr: (int) ($data['subtotal_idr'] ?? 0),
            platformFeeIdr: (int) ($data['platform_fee_idr'] ?? 0),
            totalIdr: (int) ($data['total_idr'] ?? 0),
            notes: $data['notes'] ?? null,
        );
    }
}
