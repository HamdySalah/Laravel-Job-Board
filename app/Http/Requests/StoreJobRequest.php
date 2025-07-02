<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreJobRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole('employer');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:50'],
            'location' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'type' => ['required', 'string', Rule::in(['full-time', 'part-time', 'remote', 'contract', 'internship'])],
            'experience_level' => ['nullable', 'string', Rule::in(['entry', 'mid', 'senior', 'executive'])],
            'salary_min' => ['nullable', 'numeric', 'min:0'],
            'salary_max' => ['nullable', 'numeric', 'min:0', 'gte:salary_min'],
            'deadline' => ['required', 'date', 'after:today'],
            'company_logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'The job title is required.',
            'description.required' => 'The job description is required.',
            'description.min' => 'The job description must be at least 50 characters.',
            'location.required' => 'The job location is required.',
            'category.required' => 'Please select a job category.',
            'type.required' => 'Please select a job type.',
            'type.in' => 'The selected job type is invalid.',
            'experience_level.in' => 'The selected experience level is invalid.',
            'salary_min.numeric' => 'The minimum salary must be a number.',
            'salary_max.numeric' => 'The maximum salary must be a number.',
            'salary_max.gte' => 'The maximum salary must be greater than or equal to the minimum salary.',
            'deadline.required' => 'The application deadline is required.',
            'deadline.after' => 'The application deadline must be a future date.',
            'company_logo.image' => 'The company logo must be an image.',
            'company_logo.mimes' => 'The company logo must be a file of type: jpeg, png, jpg, gif.',
            'company_logo.max' => 'The company logo may not be greater than 2MB.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'salary_min' => 'minimum salary',
            'salary_max' => 'maximum salary',
            'experience_level' => 'experience level',
            'company_logo' => 'company logo',
        ];
    }
}
