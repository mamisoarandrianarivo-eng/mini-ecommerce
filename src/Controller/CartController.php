<?php

namespace App\Controller;

use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/panier')]
class CartController extends AbstractController
{
    #[Route('/', name: 'app_cart_index')]
    public function index(CartService $cartService): Response
    {
        return $this->render('cart/index.html.twig', [
            'items' => $cartService->getCartItems(),
            'total' => $cartService->getTotal(),
        ]);
    }

    #[Route('/ajouter/{id}', name: 'app_cart_add', methods: ['POST'])]
    public function add(int $id, Request $request, CartService $cartService): Response
    {
        $quantity = (int) $request->request->get('quantity', 1);
        $cartService->add($id, $quantity);
        $this->addFlash('success', 'Produit ajouté au panier !');

        return $this->redirect($request->headers->get('referer', $this->generateUrl('app_cart_index')));
    }

    #[Route('/modifier/{id}', name: 'app_cart_update', methods: ['POST'])]
    public function update(int $id, Request $request, CartService $cartService): Response
    {
        $quantity = (int) $request->request->get('quantity', 1);
        $cartService->update($id, $quantity);
        $this->addFlash('success', 'Panier mis à jour.');

        return $this->redirectToRoute('app_cart_index');
    }

    #[Route('/supprimer/{id}', name: 'app_cart_remove')]
    public function remove(int $id, CartService $cartService): Response
    {
        $cartService->remove($id);
        $this->addFlash('success', 'Produit retiré du panier.');

        return $this->redirectToRoute('app_cart_index');
    }

    #[Route('/vider', name: 'app_cart_clear')]
    public function clear(CartService $cartService): Response
    {
        $cartService->clear();
        $this->addFlash('success', 'Panier vidé.');

        return $this->redirectToRoute('app_cart_index');
    }
}
