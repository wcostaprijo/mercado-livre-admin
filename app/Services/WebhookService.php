<?php

namespace App\Services;

class WebhookService
{
    public function __construct(
        private MercadoLivreService $mercadoLivreService,
        private ProductService $productService,
        private OrderService $orderService
    ) { }

    public function mercadolivre(array $data)
    {
        if(empty($data['application_id']) || strval($data['application_id']) !== strval(config('services.mercadolivre.client_id'))) {
            return ['message' => 'Aplicação não autorizada.', 'status' => 401];
        }

        switch($data['topic'] ?? '') {
            case 'items':
                $this->productService->syncMercadoLivreProducts(substr($data['resource'] ?? '', strlen('/items/')));
                return ['message' => 'OK.', 'status' => 200];
            case 'orders_v2':
                return $this->orderService->updateOrCreate($data);
            
            default: return ['message' => 'Tópico não configurado.', 'status' => 404];
        }
    }
}
