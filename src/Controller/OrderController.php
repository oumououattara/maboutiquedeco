<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Form\OrderType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/order", name="order")
     */
    public function index(Cart $cart): Response
    {
        if (!$this->getUser()->getAddresses()->getValues()) {
            return $this->redirectToRoute('profile_address_add');
        }
        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);
        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'cart' => $cart->getFullCart()
        ]);
    }

    public function add(Cart $cart, Request $request): Response
    {
        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $date = new \DateTimeImmutable();
            $carriers = $form->get('carriers')->getData();
            $delivery = $form->get('addresses')->getData();

            $delivery_content = $delivery->getLastname().' '.$delivery->getFirstname();
            $delivery_content .= '<br>'.$delivery->getName();
            $delivery_content .= '<br>'.$delivery->getCity(). ' '.$delivery->getCountry().', '.$delivery->getZipcode();
            $delivery_content .= '<br>'.$delivery->getTelephone();

            $order = new Order();
            $reference = $date->format('dmY').'-'.uniqid();
            $order->setReference($reference);
            $order->setUser($this->getUser());
            $order->setCarrierName($carriers->getName());
            $order->setCarrierPrice($carriers->getPrice());
            $order->setCreatedAt($date);
            $order->setDelivery($delivery_content);
            $order->setState(0);
            $this->entityManager->persist($order);

            foreach ($cart->getFullCart() as $product) {
                $orderDetails = new OrderDetails();
                $orderDetails->setProduct($product['product']->getName());
                $orderDetails->setQuantity($product['quantity']);
                $orderDetails->setTotal($product['product']->getPrice() * $product['quanity']);
                $orderDetails->setPrice($product['product']->getPrice());
                $this->entityManager->persist($orderDetails);
            }
            $this->entityManager->flush();

            return $this->render('order/add.html.twig', [
                'cart' => $cart->getFullCart(),
                'carrier' => $carriers,
                'delivery' => $delivery_content,
                'reference' => $order->getReference()
            ]);
        }
        return $this->redirectToRoute('cart');
    }
}
