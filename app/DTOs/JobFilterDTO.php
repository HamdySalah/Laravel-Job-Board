<?php

namespace App\DTOs;

class JobFilterDTO
{
    public function __construct(
        public readonly ?string $search = null,
        public readonly ?string $location = null,
        public readonly ?string $category = null,
        public readonly ?string $type = null,
        public readonly ?string $experienceLevel = null,
        public readonly ?float $salaryMin = null,
        public readonly ?float $salaryMax = null,
        public readonly ?int $postedDays = null,
        public readonly ?bool $approved = null,
        public readonly string $sort = 'created_at',
        public readonly string $order = 'desc'
    ) {}

    /**
     * Create from request array
     *
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            search: $data['search'] ?? null,
            location: $data['location'] ?? null,
            category: $data['category'] ?? null,
            type: $data['type'] ?? null,
            experienceLevel: $data['experience_level'] ?? null,
            salaryMin: isset($data['salary_min']) ? (float) $data['salary_min'] : null,
            salaryMax: isset($data['salary_max']) ? (float) $data['salary_max'] : null,
            postedDays: isset($data['posted_days']) ? (int) $data['posted_days'] : null,
            approved: isset($data['approved']) ? (bool) $data['approved'] : null,
            sort: $data['sort'] ?? 'created_at',
            order: $data['order'] ?? 'desc'
        );
    }

    /**
     * Convert to array for database queries
     *
     * @return array
     */
    public function toArray(): array
    {
        return array_filter([
            'search' => $this->search,
            'location' => $this->location,
            'category' => $this->category,
            'type' => $this->type,
            'experience_level' => $this->experienceLevel,
            'salary_min' => $this->salaryMin,
            'salary_max' => $this->salaryMax,
            'posted_days' => $this->postedDays,
            'approved' => $this->approved,
            'sort' => $this->sort,
            'order' => $this->order,
        ], fn($value) => $value !== null);
    }
}
