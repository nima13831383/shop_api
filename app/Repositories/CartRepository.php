<?php

namespace App\Repositories;

use App\Models\Cart;
use Symfony\Component\HttpFoundation\Request;

class CartRepository
{
    public function getCartItems($identifier)
    {
        return Cart::where('user_id', $identifier)
            ->orWhere('guest_id', $identifier)
            ->with('product')
            ->get();
    }

    public function findItem($identifier, $productId): ?Cart
    {
        return Cart::where(function ($q) use ($identifier) {
            $q->where('user_id', $identifier)
                ->orWhere('guest_id', $identifier);
        })
            ->where('product_id', $productId)
            ->first();
    }

    public function create(array $data): Cart
    {
        return Cart::create($data);
    }

    public function update(Cart $cart, array $data): Cart
    {
        $cart->update($data);
        return $cart;
    }

    public function delete(Cart $cart): bool
    {
        return $cart->delete();
    }
}
