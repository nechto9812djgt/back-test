<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactRequest;
use Illuminate\Http\JsonResponse;
use App\Services\ContactService;

class ContactController extends Controller
{
    public function __construct(private ContactService $contactService)
    {}

    public function store(ContactRequest $request): JsonResponse
    {
        $result = $this->contactService->handle(
            $request->validated(),
            $request->ip()
        );
        return response()->json($result);
    }
}
