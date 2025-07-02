<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Job extends Model
{
    protected $table = 'job_posts';

    protected $fillable = [
        'title', 'description', 'location', 'type', 'experience_level', 'salary_min', 'salary_max',
        'deadline', 'category', 'employer_id', 'is_approved', 'company_logo'
    ];

    public function employer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employer_id');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    // Helper method to maintain compatibility with existing code
    public function getStatusAttribute()
    {
        return $this->is_approved ? 'approved' : 'pending';
    }

    // Helper method to maintain compatibility with existing code
    public function setStatusAttribute($value)
    {
        $this->attributes['is_approved'] = ($value === 'approved');
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopePending($query)
    {
        return $query->where('is_approved', false);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByLocation($query, string $location)
    {
        return $query->where('location', 'like', "%{$location}%");
    }

    public function scopeByExperienceLevel($query, string $level)
    {
        return $query->where('experience_level', $level);
    }

    public function scopeWithinSalaryRange($query, ?float $min = null, ?float $max = null)
    {
        if ($min !== null) {
            $query->where('salary_max', '>=', $min);
        }

        if ($max !== null) {
            $query->where('salary_min', '<=', $max);
        }

        return $query;
    }

    public function scopePostedWithinDays($query, int $days)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('location', 'like', "%{$search}%");
        });
    }

    // Accessors
    public function getFormattedSalaryRangeAttribute(): ?string
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

    public function getIsExpiredAttribute(): bool
    {
        return $this->deadline->isPast();
    }

    public function getDaysUntilDeadlineAttribute(): int
    {
        return $this->deadline->diffInDays(now());
    }
}
