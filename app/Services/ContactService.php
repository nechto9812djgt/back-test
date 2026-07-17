<?php

namespace App\Services;

use Illuminate\Http\Exceptions\HttpResponseException;
use App\Services\AIService;

class ContactService
{
    public function __construct(
        private RateLimitService $rateLimitService,
        private MetricsService $metricsService,
        private MailService $mailService,
        private AIService $aiService,
        private LogService $logService,
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

        $sentiment = $this->aiService->analyzeSentiment($data['comment']);

        $this->metricsService->increment($sentiment);
        $this->logService->contact($data, 'success');
        $this->mailService->send($data);

        return [
            'success' => true,
            'message' => 'Request received',
            //'data' => $data,
            'data' => $sentiment
        ];
    }
}
