<?php

namespace App\Classe;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class Cart
 * @package App\Classe
 */
class Cart
{
    /**
     * Doctrine
     *
     * @var [type]
     */
    private $entityManager;

    /**
     * Session
     *
     * @var [type]
     */
    private $session;

    /**
     * Cart constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param SessionInterface $session
     */
    public function __construct(EntityManagerInterface $entityManager, SessionInterface $session)
    {
        $this->entityManager = $entityManager;
        $this->session = $session;
    }

    public function get()
    {
        return $this->session->get('cart');
    }

    public function add($id)
    {
        $cart = $this->session->get('cart', []);
        if (!empty($cart[$id])) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }
        $this->session->set('cart', $cart);
    }

    public function decrease($id)
    {
        $cart = $this->session->get('cart', []);
        if ($cart[$id] > 1) {
            $cart[$id]--;
        } else {
            unset($cart[$id]);
        }
        return $this->session->set('cart', $cart);
    }

    public function remove()
    {
        return $this->session->remove('cart');
    }

    public function delete($id)
    {
        $cart = $this->session->get('cart', []);
        unset($cart[$id]);
        return $this->session->set('cart', $cart);
    }

    public function getFullCart()
    {
        $cart_complete = [];
        if ($this->get()) {
            foreach ($this->get() as $id => $quantity) {
                $product_cart = $this->entityManager->getRepository(Product::class)->findOneBy(['id' => $id]);
                if (!$product_cart) {
                    $this->delete($id);
                    continue;
                }
                $cart_complete[] = [
                    'product' => $product_cart,
                    'quantity' => $quantity
                ];
            }
        }
        return $cart_complete;
    }
}