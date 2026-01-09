<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Inertia\Response;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function __construct(private Order $model)
    { }
    
    public function index(Request $request): Response
    {
        return Inertia::render('Orders/Index', [
            'items' => $this->model->search($request)->with('items')->latest('id')->paginate($request->limit ?? 5),
            'search' => $request->search ?? ''
        ]);
    }

    public function show(Order $order): Response
    {
        return Inertia::render('Orders/Show', [
            'order' => $order->load('items.product.images'),
        ]);
    }
}
