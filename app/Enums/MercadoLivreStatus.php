<?php

namespace App\Enums;

enum MercadoLivreStatus: string
{
    case ATIVO = 'active';
    case INATIVO = 'inactive';
    case EM_REVISAO = 'under_review';
    case PAUSADO = 'paused';
    case FINALIZADO = 'closed';
    case PAGAMENTO_PENDENTE = 'payment_required';
    case EXCLUIDO = 'deleted';
    case CONFIRMADO = 'confirmed';
    case PAGAMENTO_EM_PROCESSO = 'payment_in_process';
    case PARCIALMENTE_PAGO = 'partially_paid';
    case PAGO = 'paid';
    case PARCIALMENTE_DEVOLVIDO = 'partially_refunded';
    case AGUARDANDO_CANCELAMENTO = 'pending_cancel';
    case CANCELADO = 'cancelled';
    case INVALIDO = 'invalid';

    public function label(): string
    {
        return match($this) {
            self::ATIVO => 'Ativo',
            self::INATIVO => 'Inativo',
            self::EM_REVISAO => 'Em Revisão',
            self::PAUSADO => 'Pausado',
            self::FINALIZADO => 'Finalizado',
            self::PAGAMENTO_PENDENTE => 'Pagamento Pendente',
            self::EXCLUIDO => 'Excluído',
            self::CONFIRMADO => 'Confirmado',
            self::PAGAMENTO_EM_PROCESSO => 'Pagamento em Processo',
            self::PARCIALMENTE_PAGO => 'Parcialmente Pago',
            self::PAGO => 'Pago',
            self::PARCIALMENTE_DEVOLVIDO => 'Parcialmente Devolvido',
            self::AGUARDANDO_CANCELAMENTO => 'Aguardando Cancelamento',
            self::CANCELADO => 'Cancelado',
            self::INVALIDO => 'Inválido',
        };
    }
}
