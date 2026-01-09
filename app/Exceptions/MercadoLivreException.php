<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;

class MercadoLivreException extends Exception
{
    function __construct(string $message)
    {
        $this->message = $message;
    }

    public function report(): void
    {
        Log::channel('stack')->error("mercado_livre: {$this->message}"); 
    }

    public function render(): RedirectResponse
    { 
        return redirect()->back()->withErrors(['message' => $this->message]);
    }
}
