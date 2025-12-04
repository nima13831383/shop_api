<?php

namespace App\Services\Api\Shop;

use App\Repositories\CartRepository;

class CartService
{
    protected $repo;

    public function __construct(CartRepository $repo)
    {
        $this->repo = $repo;
    }


    /**
     * تشخیص اینکه درخواست مربوط به یوزر است یا مهمان
     */
    private function identifier($request)
    {
        if ($request->user()) {
            return [
                'type' => 'user',
                'id'   => $request->user()->id,
            ];
        }

        return [
            'type' => 'guest',
            'id'   => $request->guest_id,
        ];
    }


    /**
     * بررسی مالکیت cart
     */
    private function isOwner($cart, $identifier)
    {
        if ($identifier['type'] === 'user') {
            return $cart->user_id === $identifier['id'];
        }

        if ($identifier['type'] === 'guest') {
            return $cart->guest_id === $identifier['id'];
        }

        return false;
    }


    /**
     * گرفتن لیست آیتم‌های سبد
     */
    public function get($request)
    {
        return $this->repo->getCartItems(
            $this->identifier($request)['id']
        );
    }


    /**
     * افزودن محصول به cart
     */
    public function add($request)
    {
        $identifier = $this->identifier($request);

        // بررسی موجود بودن محصول در cart (برای همان مالک)
        $existing = $this->repo->findItem($identifier['id'], $request->product_id);

        if ($existing) {
            return $this->repo->update($existing, [
                'quantity' => $existing->quantity + $request->quantity
            ]);
        }

        // ایجاد cart جدید
        return $this->repo->create([
            'user_id'    => $identifier['type'] === 'user' ? $identifier['id'] : null,
            'guest_id'   => $identifier['type'] === 'guest' ? $identifier['id'] : null,
            'product_id' => $request->product_id,
            'quantity'   => $request->quantity,
        ]);
    }


    /**
     * آپدیت cart فقط برای مالک
     */
    public function update($request, $cart)
    {
        $identifier = $this->identifier($request);

        if (!$this->isOwner($cart, $identifier)) {
            abort(403, 'You are not allowed to update this cart item.');
        }

        return $this->repo->update($cart, $request->validated());
    }


    /**
     * حذف cart فقط برای مالک
     */
    public function delete($cart, $request)
    {
        $identifier = $this->identifier($request);

        if (!$this->isOwner($cart, $identifier)) {
            abort(403, 'You are not allowed to delete this cart item.');
        }

        return $this->repo->delete($cart);
    }
}
