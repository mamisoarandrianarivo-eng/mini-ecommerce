<?php

namespace App\Service;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
    private const CART_KEY = 'cart';

    public function __construct(
        private RequestStack $requestStack,
        private ProductRepository $productRepository
    ) {}

    private function getSession(): SessionInterface
    {
        return $this->requestStack->getSession();
    }

    public function getCart(): array
    {
        return $this->getSession()->get(self::CART_KEY, []);
    }

    public function add(int $productId, int $quantity = 1): void
    {
        $cart = $this->getCart();
        if (isset($cart[$productId])) {
            $cart[$productId] += $quantity;
        } else {
            $cart[$productId] = $quantity;
        }
        $this->getSession()->set(self::CART_KEY, $cart);
    }

    public function remove(int $productId): void
    {
        $cart = $this->getCart();
        unset($cart[$productId]);
        $this->getSession()->set(self::CART_KEY, $cart);
    }

    public function update(int $productId, int $quantity): void
    {
        $cart = $this->getCart();
        if ($quantity <= 0) {
            unset($cart[$productId]);
        } else {
            $cart[$productId] = $quantity;
        }
        $this->getSession()->set(self::CART_KEY, $cart);
    }

    public function clear(): void
    {
        $this->getSession()->remove(self::CART_KEY);
    }

    /**
     * @return array{product: Product, quantity: int}[]
     */
    public function getCartItems(): array
    {
        $cart = $this->getCart();
        $items = [];

        foreach ($cart as $productId => $quantity) {
            $product = $this->productRepository->find($productId);
            if ($product) {
                $items[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                ];
            }
        }

        return $items;
    }

    public function getTotal(): float
    {
        $total = 0;
        foreach ($this->getCartItems() as $item) {
            $total += (float) $item['product']->getPrice() * $item['quantity'];
        }
        return $total;
    }

    public function getItemCount(): int
    {
        return array_sum($this->getCart());
    }
}
