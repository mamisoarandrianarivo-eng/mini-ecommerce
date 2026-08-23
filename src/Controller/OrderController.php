<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Repository\OrderRepository;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/commandes')]
#[IsGranted('ROLE_USER')]
class OrderController extends AbstractController
{
    #[Route('/', name: 'app_order_index')]
    public function index(OrderRepository $orderRepository): Response
    {
        $orders = $orderRepository->findByUser($this->getUser());

        return $this->render('order/index.html.twig', [
            'orders' => $orders,
        ]);
    }

    #[Route('/commander', name: 'app_order_checkout')]
    public function checkout(Request $request, CartService $cartService, EntityManagerInterface $em): Response
    {
        $items = $cartService->getCartItems();
        if (empty($items)) {
            $this->addFlash('warning', 'Votre panier est vide.');
            return $this->redirectToRoute('app_cart_index');
        }

        if ($request->isMethod('POST')) {
            $address = $request->request->get('address');

            $order = new Order();
            $order->setUser($this->getUser());
            $order->setShippingAddress($address);

            foreach ($items as $item) {
                $orderItem = new OrderItem();
                $orderItem->setProduct($item['product']);
                $orderItem->setQuantity($item['quantity']);
                $orderItem->setUnitPrice($item['product']->getPrice());
                $order->addItem($orderItem);

                // Décrémenter le stock
                $product = $item['product'];
                $product->setStock($product->getStock() - $item['quantity']);
            }

            $order->calculateTotal();
            $order->setStatus(Order::STATUS_PAID);

            $em->persist($order);
            $em->flush();

            $cartService->clear();

            $this->addFlash('success', 'Commande passée avec succès ! Référence : ' . $order->getReference());
            return $this->redirectToRoute('app_order_show', ['id' => $order->getId()]);
        }

        return $this->render('order/checkout.html.twig', [
            'items' => $items,
            'total' => $cartService->getTotal(),
        ]);
    }

    #[Route('/{id}', name: 'app_order_show')]
    public function show(Order $order): Response
    {
        // Vérifier que la commande appartient à l'utilisateur
        if ($order->getUser() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas voir cette commande.');
        }

        return $this->render('order/show.html.twig', [
            'order' => $order,
        ]);
    }
}
