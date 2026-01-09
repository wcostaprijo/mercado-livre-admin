<?php

namespace App\Services;

use App\Exceptions\ProductException;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ProductService
{
    public function __construct(private MercadoLivreService $mercadoLivreService)
    { }

    public function store(User $user, array $data): Product | array
    {
        try {
            $productResponse = $this->mercadoLivreService->createProduct([
                'title' => $data['title'],
                'category_id' => $data['mercado_livre_category_id'],
                'currency_id' => 'BRL',
                'price' => $data['price'],
                'available_quantity' => $data['stock_quantity'],
                'condition' => 'new',
                'buying_mode' => 'buy_it_now',
                'listing_type_id' => $data['listing_type'],
                'description' => $data['description'],
                'attributes' => array_map(fn($attr) => [
                    'id' => $attr['id'],
                    'value_name' => !empty($attr['unit']) ? $attr['value_name'] . ' '. $attr['unit'] : $attr['value_name'],
                ], $data['attributes']),
                'shipping' => [
                    'mode' => 'not_specified',
                    'free_shipping' => false,
                    'local_pick_up' => false,
                ],
                'pictures' => array_map(fn($img) => ['source' => $img], $data['pictures'] ?? []),
            ]);

            if(!empty($productResponse['errors'])) {
                return array_map(fn($cause) => $cause['message'], $productResponse['errors']);
            }

            $product = $user->products()->create([
                'mercado_livre_id' => $productResponse['id'],
                'mercado_livre_category_id' => $data['mercado_livre_category_id'],
                'mercado_livre_category_path' => $data['mercado_livre_category_path'],
                'title' => $data['title'],
                'description' => $data['description'],
                'price' => $data['price'],
                'stock_quantity' => $data['stock_quantity'],
                'attributes' => $data['attributes'],
                'status' => $productResponse['status'],
                'listing_type' => $data['listing_type'],
                'product_url' => $productResponse['permalink'],
            ]);

            $product->images()->createMany(array_map(fn($img) => ['url' => $img], $data['pictures'] ?? []));

            if(empty($user->mercado_livre_id)) {
                $user->update(['mercado_livre_id' => $productResponse['seller_id']]);
            }

            return $product;
        } catch (\Exception $e) {
            throw new ProductException($e->getMessage());
        }
    }

    public function update(Product $product, array $data): Product | array
    {
        try {
            $fieldsToUpdate = array_filter([
                'title' => $data['title'] ?? null,
                'price' => $data['price'] ?? null,
                'available_quantity' => $data['stock_quantity'] ?? null,
                'pictures' => isset($data['pictures']) ? array_map(fn($img) => ['source' => $img], $data['pictures']) : null,
            ]);

            if(!empty($fieldsToUpdate)) {
                if(!empty($product->mercado_livre_id)) {
                    $productResponse = $this->mercadoLivreService->updateProduct($product->mercado_livre_id, $fieldsToUpdate);
    
                    if(!empty($productResponse['errors'])) {
                        return array_map(fn($cause) => $cause['message'], $productResponse['errors']);
                    }
                }

                $product->update([
                    'title' => $data['title'] ?? $product->title,
                    'description' => $data['description'] ?? $product->description,
                    'price' => $data['price'] ?? $product->price,
                    'stock_quantity' => $data['stock_quantity'] ?? $product->stock_quantity,
                ]);

                if (!empty($data['pictures'])) {
                    $product->images()->delete();
                    $product->images()->createMany(array_map(fn($img) => ['url' => $img], $data['pictures'] ?? []));
                }

                $product->refresh();
            }

            return $product;
        } catch (\Exception $e) {
            throw new ProductException($e->getMessage());
        }
    }

    public function uploadImages(array $images): array
    {
        $uploaded = [];
        foreach ($images as $image) {
            $path = Storage::putFile('products', $image, 'public');

            $uploaded[] = [
                'url' => Storage::url($path),
            ];
        }

        return $uploaded;
    }

    public function syncMercadoLivreProducts(?string $itemId = null): void
    {
        Product::whereNotNull('mercado_livre_id')->when(
            $itemId,
            fn($query) => $query->where('mercado_livre_id', $itemId)
        )->chunk(50, function ($products) {
            foreach ($products as $product) {
                $this->mercadoLivreService->setUser($product->user);

                try {
                    $productData = $this->mercadoLivreService->getProduct($product->mercado_livre_id);

                    if (!empty($productData['errors'])) {
                        Log::channel('mercadolivre')->warning('Erro ao obter dados do produto', [
                            'product_id' => $product->id,
                            'mercado_livre_id' => $product->mercado_livre_id,
                            'errors' => $productData['errors'],
                        ]);
                        continue;
                    }

                    if (!empty($productData['available_quantity'])) {
                        $product->stock_quantity = $productData['available_quantity'];
                    }

                    $finalPrices = [];
                    $promotionsResponse = $this->mercadoLivreService->getPromotions($product->mercado_livre_id);
                    if (empty($promotionsResponse['errors']) && is_array($promotionsResponse)) {
                        foreach ($promotionsResponse as $promotion) {
                            $price = $this->resolvePromotionPrice($promotion);

                            if ($price !== null) {
                                $finalPrices[] = $price;
                            }
                        }
                    }

                    if (!empty($finalPrices)) {
                        $product->price = min($finalPrices);
                    } else {
                        $product->price = $productData['price'];
                    }

                    $product->status = $productData['status'];
                    $product->save();

                    Log::channel('mercadolivre')->info('Produto sincronizado com sucesso', [
                        'product_id' => $product->id,
                        'mercado_livre_id' => $product->mercado_livre_id,
                        'stock_quantity' => $product->stock_quantity,
                        'original_price' => $productData['price'],
                        'final_price' => $product->price,
                        'promotion_applied' => !empty($finalPrices),
                    ]);

                } catch (\Throwable $e) {
                    Log::channel('mercadolivre')->error('Erro ao sincronizar produto', [
                        'product_id' => $product->id,
                        'mercado_livre_id' => $product->mercado_livre_id,
                        'exception' => $e->getMessage(),
                    ]);
                }
            }
        });
    }

    private function resolvePromotionPrice(array $promotion): ?float
    {
        if (($promotion['status'] ?? null) !== 'started') {
            return null;
        }

        if (!empty($promotion['start_date']) && !empty($promotion['finish_date'])) {
            $now = Carbon::now();

            if ($now->lt(Carbon::parse($promotion['start_date'])) || $now->gt(Carbon::parse($promotion['finish_date']))) {
                return null;
            }
        }

        if (!empty($promotion['price']) && $promotion['price'] > 0) {
            return (float) $promotion['price'];
        }

        $originalPrice = $promotion['original_price'] ?? null;
        if (!$originalPrice) {
            return null;
        }

        if (!empty($promotion['fixed_percentage'])) {
            return round($originalPrice * (1 - ($promotion['fixed_percentage'] / 100)), 2);
        }

        if (!empty($promotion['fixed_amount'])) {
            return max(0, round($originalPrice - $promotion['fixed_amount'], 2));
        }

        return null;
    }
}
