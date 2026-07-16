<?php

namespace App\Services;

use Illuminate\Http\Exceptions\HttpResponseException;

class ContactService
{
    public function __construct(
        private RateLimitService $rateLimitService,
        private MetricsService $metricsService,
        private MailService $mailService,
        private AIService $aiService,
        private LogService $logService
    ) {
    }

    public function handle(array $data, string $ip): array
    {
        if (!$this->rateLimitService->check($ip)) {
            throw new HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => 'Too many requests.'
                ], 429)
            );
        }

        $this->metricsService->increment('unknown');
        $this->logService->contact($data, 'success');
        $this->mailService->send($data);

        return [
            'success' => true,
            'message' => 'Request received',
            'data' => $data,
        ];
    }
}
