<?php

namespace App\Http\Controllers;

use App\Services\MercadoLivreService;
use Inertia\Response;
use Inertia\Inertia;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(private MercadoLivreService $mercadoLivreService)
    { }

    public function index(Request $request): Response
    {
        $token = $request->user()->mercadoLivreToken;

        if($token && $token->expires_at->isPast()) {
            $this->mercadoLivreService->refreshToken($token);
            $token->refresh();
        }

        return Inertia::render('Dashboard', [
            'connected' => $token && $token->expires_at->isFuture(),
            'authorization_url' => $this->mercadoLivreService->getAuthorizationUrl(),
        ]);
    }
}
