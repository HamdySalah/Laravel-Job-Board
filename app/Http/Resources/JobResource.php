<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'location' => $this->location,
            'category' => $this->category,
            'type' => $this->type,
            'experience_level' => $this->experience_level,
            'salary_min' => $this->salary_min,
            'salary_max' => $this->salary_max,
            'deadline' => $this->deadline,
            'is_approved' => $this->is_approved,
            'company_logo' => $this->company_logo ? asset("storage/{$this->company_logo}") : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Relationships
            'employer' => new UserResource($this->whenLoaded('employer')),
            'applications_count' => $this->when(isset($this->applications_count), $this->applications_count),

            // Computed attributes
            'formatted_created_at' => $this->created_at->diffForHumans(),
            'formatted_deadline' => $this->deadline->format('M d, Y'),
            'days_until_deadline' => $this->deadline->diffInDays(now()),
            'is_expired' => $this->deadline->isPast(),
            'salary_range' => $this->formatSalaryRange(),
            'status' => $this->is_approved ? 'approved' : 'pending',
            'status_badge_class' => $this->is_approved ? 'success' : 'warning',
        ];
    }

    /**
     * Format salary range for display
     *
     * @return string|null
     */
    private function formatSalaryRange(): ?string
    {
        if (!$this->salary_min && !$this->salary_max) {
            return null;
        }

        if ($this->salary_min && $this->salary_max) {
            return '$' . number_format($this->salary_min) . ' - $' . number_format($this->salary_max);
        }

        if ($this->salary_min) {
            return 'From $' . number_format($this->salary_min);
        }

        return 'Up to $' . number_format($this->salary_max);
    }
}
