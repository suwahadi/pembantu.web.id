<?php

namespace App\Domain\Worker\Services;

use App\Models\{Worker, ServiceCategory, Location};
use App\Domain\Worker\DTO\WorkerSearchDTO;

class WorkerCatalogService
{
    /**
     * Search workers dengan filter
     */
    public function search(WorkerSearchDTO $dto)
    {
        $query = Worker::query()
            ->where('is_available', true)
            ->where('verification_status', 'verified');

        if ($dto->categoryId) {
            $query->where('category_id', $dto->categoryId);
        }

        if ($dto->locationId) {
            // Filter by worker service areas instead of direct location
            $query->whereHas('serviceAreas', function ($q) use ($dto) {
                $q->where('location_id', $dto->locationId)
                  ->where('is_active', true);
            });
        }

        if ($dto->minRating !== null) {
            $query->where('rating', '>=', $dto->minRating);
        }

        if ($dto->agencyId) {
            $query->where('agency_id', $dto->agencyId);
        }

        if ($dto->search) {
            $query->where(function ($q) use ($dto) {
                $q->where('name', 'like', "%{$dto->search}%")
                    ->orWhere('bio', 'like', "%{$dto->search}%");
            });
        }

        // Sorting
        match($dto->sortBy) {
            'rating' => $query->orderBy('rating', 'desc'),
            'reviews' => $query->orderBy('total_reviews', 'desc'),
            'experience' => $query->orderBy('experience_years', 'desc'),
            'newest' => $query->orderBy('created_at', 'desc'),
            'price_asc' => $query->orderByRaw('(SELECT MIN(price_idr) FROM worker_service_pricings WHERE worker_id = workers.id AND is_active = 1) ASC'),
            'price_desc' => $query->orderByRaw('(SELECT MIN(price_idr) FROM worker_service_pricings WHERE worker_id = workers.id AND is_active = 1) DESC'),
            default => $query->orderBy('rating', 'desc'),
        };

        return $query->paginate($dto->perPage ?? 15);
    }

    /**
     * Get detail worker dengan relations
     */
    public function getDetail(int $workerId): ?Worker
    {
        return Worker::with([
            'agency',
            'category',
            'documents',
            'skills',
            'primarySkills',
            'pricings',
            'defaultPricing',
            'serviceAreas.location',
            'primaryServiceArea.location',
        ])->find($workerId);
    }

    /**
     * Get workers untuk category
     */
    public function getByCategoryId(int $categoryId, int $limit = 20)
    {
        return Worker::where('category_id', $categoryId)
            ->where('is_available', true)
            ->where('verification_status', 'verified')
            ->orderBy('rating', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get workers untuk agency
     */
    public function getByAgencyId(int $agencyId)
    {
        return Worker::where('agency_id', $agencyId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get featured workers (top rated)
     */
    public function getFeatured(int $limit = 10)
    {
        return Worker::where('is_available', true)
            ->where('verification_status', 'verified')
            ->where('total_reviews', '>', 0)
            ->orderBy('rating', 'desc')
            ->orderBy('total_reviews', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get recently active workers
     */
    public function getRecentlyActive(int $limit = 10)
    {
        return Worker::where('is_available', true)
            ->where('verification_status', 'verified')
            ->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function findPublicByPublicId(string $publicId): ?object
    {
        return \Illuminate\Support\Facades\DB::table('workers')
            ->join('agencies','agencies.id','=','workers.agency_id')
            ->join('service_categories','service_categories.id','=','workers.category_id')
            ->leftJoin('worker_service_areas', function($join) {
                $join->on('worker_service_areas.worker_id', '=', 'workers.id')
                     ->where('worker_service_areas.is_primary', true)
                     ->where('worker_service_areas.is_active', true);
            })
            ->leftJoin('locations','locations.id','=','worker_service_areas.location_id')
            ->select([
                'workers.*',
                'agencies.company_name as agency_name',
                'service_categories.name as category_name',
                \Illuminate\Support\Facades\DB::raw('COALESCE(locations.city, "-") as location_name'),
            ])
            ->where('workers.public_id', $publicId)
            ->where('workers.is_available', 1)
            ->where('workers.verification_status', 'verified')
            ->where('agencies.verification_status', 'verified')
            ->first();
    }
}
