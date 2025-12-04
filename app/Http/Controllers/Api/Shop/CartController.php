<?php

namespace App\Http\Controllers\Api\Shop;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCartRequest;
use App\Http\Requests\UpdateCartRequest;
use App\Models\Cart;
use App\Services\Api\Shop\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected $service;

    public function __construct(CartService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        return response()->json([
            'status' => true,
            'data'   => $this->service->get($request)
        ]);
    }

    public function store(StoreCartRequest $request)
    {
        return response()->json([
            'status' => true,
            'data'   => $this->service->add($request)
        ]);
    }


    public function update(UpdateCartRequest $request, Cart $cart)
    {
        return response()->json([
            'status' => true,
            'data'   => $this->service->update($request, $cart)
        ]);
    }

    public function destroy(Cart $cart, Request $request)
    {
        return response()->json([
            'status' => true,
            'data'   => $this->service->delete($cart, $request)
        ]);
    }
}
