<?php

namespace App\Http\Controllers;

use App\Services\WebhookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function __construct(private WebhookService $service)
    { }

    public function mercadolivre(Request $request): JsonResponse
    {
        $response = $this->service->mercadolivre($request->all());
        return response()->json($response, $response['status'] ?? 200);
    }
}
