<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactRequest;
use Illuminate\Http\JsonResponse;
use App\Services\ContactService;
use OpenApi\Annotations as OA;

class ContactController extends Controller
{
    public function __construct(private ContactService $contactService)
    {}


/**
 * @OA\Post(
 *     path="/api/contact",
 *     summary="Send contact form",
 *     tags={"Contact"},
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name","email","phone","comment"},
 *             @OA\Property(property="name", type="string", example="Alex"),
 *             @OA\Property(property="email", type="string", example="alex@gmail.com"),
 *             @OA\Property(property="phone", type="string", example="+79999999999"),
 *             @OA\Property(property="comment", type="string", example="Your service is amazing!")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Success"
 *     ),
 *
 *     @OA\Response(
 *         response=422,
 *         description="Validation error"
 *     )
 * )
 */

    public function store(ContactRequest $request): JsonResponse
    {
        $result = $this->contactService->handle(
            $request->validated(),
            $request->ip()
        );
        return response()->json($result);
    }
}
