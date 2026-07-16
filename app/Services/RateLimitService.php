<?php

namespace App\Services;

class RateLimitService
{
    public function check(string $ip): bool
    {
        return true;
    }
}
