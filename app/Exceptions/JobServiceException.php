<?php

namespace App\Exceptions;

use Exception;

class JobServiceException extends Exception
{
    public static function jobNotFound(int $jobId): self
    {
        return new self("Job with ID {$jobId} not found.");
    }

    public static function unauthorizedAccess(): self
    {
        return new self("You are not authorized to perform this action.");
    }

    public static function jobAlreadyApproved(): self
    {
        return new self("This job has already been approved.");
    }

    public static function invalidJobData(string $message): self
    {
        return new self("Invalid job data: {$message}");
    }
}
