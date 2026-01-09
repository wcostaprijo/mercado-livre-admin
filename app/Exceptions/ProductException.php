<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;

class ProductException extends Exception
{
    function __construct(string $message)
    {
        $this->message = $message;
    }

    public function report(): void
    {
        Log::channel('stack')->error("product: {$this->message}"); 
    }

    public function render(): RedirectResponse
    { 
        return redirect()->back()->withErrors(['message' => $this->message]);
    }
}
