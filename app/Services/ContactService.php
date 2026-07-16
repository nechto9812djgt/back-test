<?php

namespace App\Services;

use Illuminate\Http\Exceptions\HttpResponseException;

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
        if (!$this->rateLimitService->check(request()->ip())) {
            throw new HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => 'Too many requests.'
                ], 429)
            );
        }

        return [
            'success' => true,
            'message' => 'Request received',
            'data' => $data,
        ];
    }
}
