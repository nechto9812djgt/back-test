<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class RateLimitService
{
    private const MAX_REQUESTS = 5;
    private const WINDOW_SECONDS = 60;

    private string $file = 'rate_limit/requests.json';

    public function check(string $ip): bool
    {
        if (!Storage::exists($this->file)) {
            Storage::put($this->file, json_encode(new \stdClass()));
        }

        $data = json_decode(Storage::get($this->file), true) ?? [];

        $now = time();

        $requests = $data[$ip] ?? [];

        $requests = array_filter(
            $requests,
            fn($timestamp) => ($now - $timestamp) < self::WINDOW_SECONDS
        );

        if (count($requests) >= self::MAX_REQUESTS) {
            return false;
        }

        $requests[] = $now;

        $data[$ip] = array_values($requests);

        Storage::put($this->file, json_encode($data, JSON_PRETTY_PRINT));
        return true;
    }
}
