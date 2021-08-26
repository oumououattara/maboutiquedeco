<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StripeController extends AbstractController
{
    /**
     * @Route("/order/create-session/{reference}", name="stripe_create_session")
     */
    public function index(EntityManagerInterface $entityManager, Cart $cart, $reference): Response
    {
       $product_stripe = [];
       $YOUR_DOMAIN = 'https://127.0.0.1:8000';
       $order = $entityManager->getRepository(Order::class)->findOneBy(['reference' => $reference]);
       if (!$order) {
           new JsonResponse(['error' => 'order']);
       }
       foreach ($order->getOrderDetails()->getValues() as $product) {
           $product_obj = $entityManager->getRepository(Product::class)->findOneBy(['name' => $product->getProduct()]);
           $product_stripe[] = [
               'price_data' => [
                   'currency' => 'usd',
                   'unit_amount' => $product->getPrice(),
                   'product_data' => [
                       'name' => $product->getProduct(),
                       'images' => [$YOUR_DOMAIN."/uploads/".$product_obj->getImage()],
                   ],
                ],
                'quantity' => $product->getQuantity(),
            ];
       }

       $product_stripe[] = [
        'price_data' => [
            'currency' => 'usd',
            'unit_amount' => $order->getCarrierPrice(),
            'product_data' => [
                'name' => $order->getCarrierName(),
                'images' => [$YOUR_DOMAIN],
            ],
         ],
         'quantity' => 1,
       ];

       Stripe::setApiKey('pk_test_51IB2KVBsLvAIlZ0eF5gvnM66ESKHxdR8BUtXlKJ8G0nplXHeWNqNeoy3dpJUHGc0KXRMNZPWQLcbMhCvjwkKnca600n9lmLQ3p');
       $checkout_session = Session::create([
           'customer_email' => $this->getUser()->getEmail(),
           'payment_method_types' => ['card'],
           'line_items' => [
               $product_stripe
           ],
           'mode' => 'payment',
           'success_url' => $YOUR_DOMAIN . '/order/success/{CHEKOUT_SESSION_ID}',
           'cancel_url' => $YOUR_DOMAIN . '/order/cancel/{CHEKOUT_SESSION_ID}',
        ]);
        $order->setStripeSessionId($checkout_session->id);
        $entityManager->flush();
        $response = new JsonResponse(['id' => $checkout_session->id]);
        return $response;
    }
}
