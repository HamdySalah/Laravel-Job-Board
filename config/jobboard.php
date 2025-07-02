<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Job Board Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options for the job board application.
    |
    */

    'pagination' => [
        'jobs_per_page' => env('JOBS_PER_PAGE', 10),
        'users_per_page' => env('USERS_PER_PAGE', 15),
        'applications_per_page' => env('APPLICATIONS_PER_PAGE', 10),
        'notifications_per_page' => env('NOTIFICATIONS_PER_PAGE', 10),
    ],

    'job_types' => [
        'full-time',
        'part-time',
        'remote',
        'contract',
        'internship',
    ],

    'experience_levels' => [
        'entry',
        'mid',
        'senior',
        'executive',
    ],

    'job_categories' => [
        'Web Development',
        'Mobile Development',
        'UI/UX Design',
        'Data Science',
        'DevOps',
        'Project Management',
        'Marketing',
        'Sales',
        'Customer Support',
        'Finance',
        'Human Resources',
        'Education',
        'Healthcare',
        'Engineering',
    ],

    'file_uploads' => [
        'max_resume_size' => env('MAX_RESUME_SIZE', 5120), // KB
        'max_logo_size' => env('MAX_LOGO_SIZE', 2048), // KB
        'allowed_resume_types' => ['pdf', 'doc', 'docx'],
        'allowed_image_types' => ['jpeg', 'png', 'jpg', 'gif'],
    ],

    'notifications' => [
        'cleanup_after_days' => env('NOTIFICATION_CLEANUP_DAYS', 30),
        'max_unread_display' => env('MAX_UNREAD_NOTIFICATIONS', 99),
        'email_enabled' => env('NOTIFICATION_EMAIL_ENABLED', true),
        'queue_enabled' => env('NOTIFICATION_QUEUE_ENABLED', true),
    ],

    'search' => [
        'min_search_length' => env('MIN_SEARCH_LENGTH', 2),
        'max_results_per_page' => env('MAX_SEARCH_RESULTS', 50),
        'default_sort' => env('DEFAULT_SORT', 'created_at'),
        'default_order' => env('DEFAULT_ORDER', 'desc'),
    ],

    'cache' => [
        'job_stats_ttl' => env('JOB_STATS_CACHE_TTL', 3600), // seconds
        'user_stats_ttl' => env('USER_STATS_CACHE_TTL', 3600), // seconds
        'categories_ttl' => env('CATEGORIES_CACHE_TTL', 86400), // seconds
    ],

    'features' => [
        'job_approval_required' => env('JOB_APPROVAL_REQUIRED', true),
        'email_verification_required' => env('EMAIL_VERIFICATION_REQUIRED', true),
        'auto_expire_jobs' => env('AUTO_EXPIRE_JOBS', true),
        'job_expiry_days' => env('JOB_EXPIRY_DAYS', 30),
    ],
];
