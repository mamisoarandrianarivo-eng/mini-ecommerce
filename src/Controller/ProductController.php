<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/produits')]
class ProductController extends AbstractController
{
    #[Route('/', name: 'app_product_index')]
    public function index(ProductRepository $productRepository, CategoryRepository $categoryRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findActiveProducts(),
            'categories' => $categoryRepository->findAll(),
            'current_category' => null,
        ]);
    }

    #[Route('/categorie/{slug}', name: 'app_product_category')]
    public function category(string $slug, ProductRepository $productRepository, CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->findOneBy(['slug' => $slug]);
        if (!$category) {
            throw $this->createNotFoundException('Catégorie non trouvée');
        }

        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findByCategory($category->getId()),
            'categories' => $categoryRepository->findAll(),
            'current_category' => $category,
        ]);
    }

    #[Route('/{slug}', name: 'app_product_show')]
    public function show(string $slug, ProductRepository $productRepository): Response
    {
        $product = $productRepository->findOneBy(['slug' => $slug, 'isActive' => true]);
        if (!$product) {
            throw $this->createNotFoundException('Produit non trouvé');
        }

        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }
}
