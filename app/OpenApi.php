<?php

namespace App;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "Developer Landing API",
    description: "REST API for contact form with AI integration"
)]
#[OA\Server(
    url: "http://127.0.0.1:8000",
    description: "Local server"
)]
class OpenApi
{
}
