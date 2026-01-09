<?php

use App\Console\Commands\SyncMercadoLivreProducts;
use Illuminate\Support\Facades\Schedule;

Schedule::command(SyncMercadoLivreProducts::class)->everyFiveMinutes();
