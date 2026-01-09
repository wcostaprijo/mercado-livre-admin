<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ProductService;

class SyncMercadoLivreProducts extends Command
{
    protected $signature = 'mercadolivre:sync-products';

    protected $description = 'Sincroniza estoque e preços dos produtos com o Mercado Livre';

    public function handle(ProductService $productService)
    {
        $this->info('Iniciando sincronização com Mercado Livre...');

        $productService->syncMercadoLivreProducts();

        $this->info('Sincronização finalizada.');

        return Command::SUCCESS;
    }
}
