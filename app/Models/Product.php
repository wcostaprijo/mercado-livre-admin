<?php

namespace App\Models;

use App\Enums\MercadoLivreStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;

class Product extends Model
{
    protected $fillable = [
        'user_id',
        'mercado_livre_id',
        'mercado_livre_category_id',
        'mercado_livre_category_path',
        'title',
        'description',
        'price',
        'stock_quantity',
        'attributes',
        'status',
        'listing_type',
        'product_url'
    ];

    protected $appends = ['status_label'];
    
    protected function casts(): array
    {
        return [
            'attributes' => 'array',
            'mercado_livre_category_path' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return MercadoLivreStatus::tryFrom($this->status)?->label() ?? 'Aguardando Publicação';
    }

    public function search(Request $request): Builder
    {
        return $this->when($request->has('search') && !empty($request->search), function($query) use($request) {
            $query->where('title', 'LIKE', '%'.$request->search.'%')
                ->orWhere('description', 'LIKE', '%'.$request->search.'%');
        })->where('user_id', $request->user()->id);
    }
}
