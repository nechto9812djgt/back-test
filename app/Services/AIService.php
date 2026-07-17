<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    public function analyzeSentiment(string $comment): string
    {
        try {
            $response = Http::post(
                'https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key=' . env('GEMINI_API_KEY'),
                [
                    'contents' => [
                        [
                            'parts' => [
                                [
                                    'text' =>
                                        "Determine the sentiment of this message. Respond with only one word: positive, neutral, or negative.\n\nMessage: {$comment}"
                                ]
                            ]
                        ]
                    ]
                ]
            );

            if (!$response->successful()) {
                return 'unknown';
            }

            $text = strtolower(
                trim(
                    $response->json('candidates.0.content.parts.0.text', '')
                )
            );

            return match ($text) {
                'positive' => 'positive',
                'negative' => 'negative',
                'neutral' => 'neutral',
                default => 'unknown',
            };

        } catch (\Throwable $e) {
            Log::error($e->getMessage());

            return 'unknown';
        }
    }
}
