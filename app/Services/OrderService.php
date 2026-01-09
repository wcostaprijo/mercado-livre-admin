<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Models\User;

class OrderService
{
    public function __construct(private MercadoLivreService $mercadoLivreService)
    { }

    public function updateOrCreate(array $data)
    {
        Log::channel('mercadolivre')->info('Webhook recebido', $data);

        $user = User::where('mercado_livre_id', $data['user_id'])->first();
        if (!$user) {
            Log::channel('mercadolivre')->warning('Usuário não encontrado', [
                'user_id' => $data['user_id']
            ]);

            return ['message' => 'Usuário não encontrado', 'status' => 404];
        }

        $this->mercadoLivreService->setUser($user);

        $orderId = substr($data['resource'] ?? '', strlen('/orders/'));
        $orderData = $this->mercadoLivreService->getOrder($orderId);
        if (!empty($orderData['errors'])) {
            Log::channel('mercadolivre')->error('Erro ao buscar pedido', [
                'order_id' => $orderId,
                'errors' => $orderData['errors']
            ]);

            return ['message' => 'Erro ao buscar pedido', 'status' => 500];
        }

        $order = $user->orders()->updateOrCreate(['mercado_livre_id' => $orderData['id']], [
            'buyer_name' => $orderData['buyer']['first_name'] ?? 'Não informado',
            'total_amount' => $orderData['total_amount'] ?? 0,
            'status' => $orderData['status'] ?? 'Não informado',
            'raw_payload' => $orderData,
        ]);

        foreach ($orderData['order_items'] ?? [] as $item) {
            $order->items()->updateOrCreate(
                ['mercado_livre_id' => $item['item']['id']],
                [
                    'title' => $item['item']['title'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                ]
            );
        }

        return ['message' => 'OK.', 'status' => 200];
    }
}
