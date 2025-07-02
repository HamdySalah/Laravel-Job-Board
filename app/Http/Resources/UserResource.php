<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'profile_picture' => $this->profile_picture ? asset('storage/' . $this->profile_picture) : null,
            'location' => $this->location,
            'phone' => $this->phone,
            'bio' => $this->bio,
            'email_verified_at' => $this->email_verified_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Role-specific data
            'company_name' => $this->when($this->role === 'employer', $this->company_name),
            'company_logo' => $this->when($this->role === 'employer' && $this->company_logo, asset('storage/' . $this->company_logo)),
            'website' => $this->when($this->role === 'employer', $this->website),
            'skills' => $this->when($this->role === 'candidate', $this->skills),
            'experience' => $this->when($this->role === 'candidate', $this->experience),
            'resume_path' => $this->when($this->role === 'candidate' && $this->resume_path, asset('storage/' . $this->resume_path)),

            // Computed attributes
            'formatted_created_at' => $this->created_at->diffForHumans(),
            'is_verified' => !is_null($this->email_verified_at),
            'avatar_url' => $this->profile_picture
                ? asset('storage/' . $this->profile_picture)
                : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF',
        ];
    }
}
