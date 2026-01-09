<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Inertia\Response;
use Inertia\Inertia;
use App\Http\Requests\Product\StoreRequest;
use App\Http\Requests\Product\UpdateRequest;
use App\Http\Requests\Product\UploadImagesRequest;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;

class ProductController extends Controller
{
    public function __construct(private ProductService $service, private Product $model)
    { }
    
    public function index(Request $request): Response
    {
        return Inertia::render('Products/Index', [
            'items' => $this->model->search($request)->with('images')->latest('id')->paginate($request->limit ?? 5),
            'search' => $request->search ?? ''
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Products/Create');
    }

    public function store(StoreRequest $request): RedirectResponse
    {
        $response = $this->service->store($request->user(), $request->validated());
        if($response instanceof Product) {
            return redirect()->route('products.index');
        }
        
        return back()->withErrors($response);
    }

    public function edit(Product $product): Response
    {
        return Inertia::render('Products/Edit', [
            'product' => $product->load('images'),
        ]);
    }

    public function update(UpdateRequest $request, Product $product): RedirectResponse
    {
        $response = $this->service->update($product, array_filter($request->validated()));
        if($response instanceof Product) {
            return redirect()->route('products.index');
        }
        
        return back()->withErrors($response);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }

    public function uploadImages(UploadImagesRequest $request): JsonResponse
    {
        return response()->json($this->service->uploadImages($request->file('images')), 201);
    }
}
