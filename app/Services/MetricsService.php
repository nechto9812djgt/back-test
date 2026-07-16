<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class MetricsService
{
    private string $file = 'metrics/stats.json';

    public function increment(string $sentiment): void
    {
        if (!Storage::exists($this->file)) {
            Storage::put($this->file, '{}');
        }

        $stats = json_decode(Storage::get($this->file), true);

        if (!is_array($stats)) {
            $stats = [];
        }

        $stats = array_merge([
            'requests' => 0,
            'positive' => 0,
            'negative' => 0,
            'neutral' => 0,
            'unknown' => 0,
        ], $stats);

        $stats['requests']++;

        if (array_key_exists($sentiment, $stats)) {
            $stats[$sentiment]++;
        } else {
            $stats['unknown']++;
        }

        Storage::put($this->file, json_encode($stats, JSON_PRETTY_PRINT));
    }

    public function get(): array
    {
        if (!Storage::exists($this->file)) {
            return [
                'requests' => 0,
                'positive' => 0,
                'negative' => 0,
                'neutral' => 0,
                'unknown' => 0
            ];
        }

        return json_decode(Storage::get($this->file), true) ?? [];
    }
}
