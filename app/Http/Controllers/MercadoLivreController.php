<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MercadoLivreService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class MercadoLivreController extends Controller
{
    public function __construct(private MercadoLivreService $mercadoLivreService)
    { }

    public function saveAuthorizationCode(Request $request): RedirectResponse
    {
        if (!$request->code) {
            return redirect()->route('dashboard')->with('error', 'Código de autorização não fornecido.');
        }

        try {
            $this->mercadoLivreService->exchangeCodeForToken($request->code);
            return redirect()->route('dashboard')->with('success', 'Conectado ao Mercado Livre com sucesso.');
        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', $e->getMessage());
        }
    }

    public function categories(?string $id = null): JsonResponse
    {
        return response()->json($this->mercadoLivreService->getCategories($id), 200);
    }

    public function attributes(string $id): JsonResponse
    {
        return response()->json($this->mercadoLivreService->getCategoryAttributes($id), 200);
    }

    public function listingTypes(): JsonResponse
    {
        return response()->json($this->mercadoLivreService->getListingTypes(), 200);
    }
}
