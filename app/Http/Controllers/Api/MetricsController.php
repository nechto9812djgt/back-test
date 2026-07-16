<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MetricsService;
use Illuminate\Http\JsonResponse;

class MetricsController extends Controller
{
    public function __construct(private MetricsService $metricsService)
    {}

    public function index(): JsonResponse
    {
        return response()->json($this->metricsService->get());
    }
}
