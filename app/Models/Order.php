<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Enums\MercadoLivreStatus;
use Illuminate\Http\Request;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'mercado_livre_id',
        'buyer_name',
        'total_amount',
        'status',
        'raw_payload',
    ];

    protected $appends = ['status_label'];
    
    protected function casts(): array
    {
        return [
            'raw_payload' => 'array',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return MercadoLivreStatus::tryFrom($this->status)?->label() ?? 'Aguardando Pagamento';
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function search(Request $request): Builder
    {
        return $this->when($request->has('search') && !empty($request->search), function($query) use($request) {
            $query->where('buyer_name', 'LIKE', '%'.$request->search.'%');
        })->where('user_id', $request->user()->id);
    }
}
