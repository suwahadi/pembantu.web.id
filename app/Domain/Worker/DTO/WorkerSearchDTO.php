<?php

namespace App\Domain\Worker\DTO;

class WorkerSearchDTO
{
    public function __construct(
        public ?int $categoryId = null,
        public ?int $locationId = null,
        public ?float $minRating = null,
        public ?int $agencyId = null,
        public ?string $search = null,
        public string $sortBy = 'rating',
        public int $perPage = 15,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            categoryId: $data['category_id'] ?? null,
            locationId: $data['location_id'] ?? null,
            minRating: isset($data['min_rating']) ? (float) $data['min_rating'] : null,
            agencyId: $data['agency_id'] ?? null,
            search: $data['search'] ?? null,
            sortBy: $data['sort_by'] ?? 'rating',
            perPage: $data['per_page'] ?? 15,
        );
    }
}
