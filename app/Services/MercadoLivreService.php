<?php

namespace App\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\PendingRequest;
use App\Exceptions\MercadoLivreException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use App\Models\MercadoLivreToken;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;

class MercadoLivreService
{
    public function __construct(
        private ?User $user,
        private Request $request, 
        private string $authUrl = '',
        private string $baseUrl = ''
    ) {
        $this->user = $request->user() ?? null;
        $this->baseUrl = config('services.mercadolivre.base_url');
        $this->authUrl = config('services.mercadolivre.auth_url');
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getAuthorizationUrl(): string
    {
        return $this->authUrl . '/authorization?' . http_build_query([
            'response_type' => 'code',
            'client_id'     => config('services.mercadolivre.client_id'),
            'redirect_uri'  => config('services.mercadolivre.redirect_uri'),
        ]);
    }

    public function exchangeCodeForToken(string $code): void
    {
        $response = Http::asForm()->post($this->baseUrl . '/oauth/token', [
            'grant_type' => 'authorization_code',
            'client_id' => config('services.mercadolivre.client_id'),
            'client_secret' => config('services.mercadolivre.client_secret'),
            'code' => $code,
            'redirect_uri' => config('services.mercadolivre.redirect_uri'),
        ])->onError(function (Response $response) {
            throw new MercadoLivreException($response->json()['message'] ?? 'Erro desconhecido');
        });

        $data = $response->json();

        MercadoLivreToken::updateOrCreate(['user_id' => $this->user->id], [
            'access_token'  => $data['access_token'],
            'refresh_token' => $data['refresh_token'],
            'expires_at'    => Carbon::now()->addSeconds($data['expires_in']),
        ]);
    }

    public function refreshToken(MercadoLivreToken $token): void
    {
        $response = Http::asForm()->post($this->baseUrl . '/oauth/token', [
            'grant_type' => 'refresh_token',
            'client_id' => config('services.mercadolivre.client_id'),
            'client_secret'=> config('services.mercadolivre.client_secret'),
            'refresh_token'=> $token->refresh_token,
        ])->onError(function (Response $response) {
            throw new MercadoLivreException($response->json()['message'] ?? 'Erro desconhecido');
        });

        $data = $response->json();

        $token->update([
            'access_token'  => $data['access_token'],
            'refresh_token' => $data['refresh_token'],
            'expires_at'    => Carbon::now()->addSeconds($data['expires_in']),
        ]);
    }

    protected function getAccessToken(): string
    {
        $token = $this->user->mercadoLivreToken;

        if (!$token) {
            throw new MercadoLivreException('Token do Mercado Livre não encontrado');
        }

        if ($token->expires_at->isPast()) {
            $this->refreshToken($token);
            $token->refresh();
        }

        return $token->access_token;
    }

    protected function client(): PendingRequest
    {
        return Http::withToken($this->getAccessToken())->acceptJson();
    }

    public function getCategories(?string $id = null): array
    {
        $endpoint = '/sites/MLB/categories';
        if ($id) {
            $endpoint = "/categories/{$id}";
        }

        return $this->handleResponse($this->client()->get($this->baseUrl . $endpoint));
    }

    public function getCategoryAttributes(string $id): array
    {
        return $this->handleResponse($this->client()->get($this->baseUrl . "/categories/{$id}/attributes"));
    }

    public function getListingTypes(): array
    {
        return $this->handleResponse($this->client()->get($this->baseUrl . '/sites/MLB/listing_types'));
    }

    public function createProduct(array $payload): array
    {
        return $this->handleResponse($this->client()->post($this->baseUrl . '/items', $payload));
    }

    public function updateProduct(string $itemId, array $payload): array
    {
        return $this->handleResponse($this->client()->put($this->baseUrl . "/items/{$itemId}", $payload));
    }

    public function getProduct(string $itemId): array
    {
        return $this->handleResponse($this->client()->get($this->baseUrl . "/items/{$itemId}"));
    }

    public function getPromotions(string $itemId): array
    {
        return $this->handleResponse($this->client()->get($this->baseUrl . "/seller-promotions/items/{$itemId}?app_version=v2"));
    }

    public function getOrder(string $orderId): array
    {
        return $this->handleResponse($this->client()->get($this->baseUrl . "/orders/{$orderId}"));
    }

    protected function handleResponse($response): array
    {
        if ($response->failed()) {
            return [
                'errors' => !empty($response->json()['cause']) ? array_filter($response->json()['cause'] ?? [], fn($cause) => $cause['type'] == 'error') : [$response->json()['message'] ?? 'Erro desconhecido'],
            ];
        }

        return $response->json();
    }
}
