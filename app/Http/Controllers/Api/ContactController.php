<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactRequest;
use Illuminate\Http\JsonResponse;

class ContactController extends Controller
{
    public function store(ContactRequest $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Request received',
            'data' => $request->validated(),
        ]);
    }
}
