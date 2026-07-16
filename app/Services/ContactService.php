<?php

namespace App\Services;

class ContactService
{
    public function __construct(
        private RateLimitService $rateLimitService,
        private MetricsService $metricsService,
        private MailService $mailService,
        private AIService $aiService
    ) {
    }

    public function handle(array $data): array
    {
        return [
            'success' => true,
            'message' => 'Request received',
            'data' => $data,
        ];
    }
}
