<?php

use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/webhooks/mercadolivre', [WebhookController::class, 'mercadolivre']);