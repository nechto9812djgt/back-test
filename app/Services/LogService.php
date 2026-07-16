<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class LogService
{
    public function contact(array $data, string $status): void
    {
        Log::build([
            'driver' => 'single',
            'path' => storage_path('logs/contact.log'),
        ])->info('Contact requests', [
            'time' => now()->toDateTimeString(),
            'ip' => request()->ip(),
            'status' => $status,
            'name' => $data['name'] ?? null,
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
        ]);
    }
}
