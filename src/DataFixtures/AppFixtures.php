<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {}

    public function load(ObjectManager $manager): void
    {
        $slugger = new AsciiSlugger();

        // Admin user
        $admin = new User();
        $admin->setEmail('admin@minishop.com');
        $admin->setFirstName('Admin');
        $admin->setLastName('MiniShop');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setAddress('123 Rue Admin, Paris');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));
        $manager->persist($admin);

        // Regular user
        $user = new User();
        $user->setEmail('user@minishop.com');
        $user->setFirstName('Jean');
        $user->setLastName('Dupont');
        $user->setAddress('456 Rue Client, Lyon');
        $user->setPassword($this->passwordHasher->hashPassword($user, 'user123'));
        $manager->persist($user);

        // Categories
        $categoriesData = [
            ['name' => 'Électronique', 'description' => 'Smartphones, ordinateurs, tablettes et accessoires électroniques'],
            ['name' => 'Vêtements', 'description' => 'Mode homme et femme, vêtements tendance'],
            ['name' => 'Maison & Déco', 'description' => 'Mobilier, décoration et accessoires pour la maison'],
            ['name' => 'Sport', 'description' => 'Équipements et vêtements de sport'],
            ['name' => 'Livres', 'description' => 'Romans, manuels, BD et magazines'],
        ];

        $categories = [];
        foreach ($categoriesData as $catData) {
            $category = new Category();
            $category->setName($catData['name']);
            $category->setSlug($slugger->slug(strtolower($catData['name']))->toString());
            $category->setDescription($catData['description']);
            $manager->persist($category);
            $categories[] = $category;
        }

        // Products
        $productsData = [
            ['name' => 'Smartphone Galaxy S24', 'description' => 'Smartphone Samsung Galaxy S24 avec écran AMOLED 6.2 pouces, 128Go de stockage, appareil photo 50MP.', 'price' => '899.99', 'stock' => 25, 'category' => 0],
            ['name' => 'MacBook Air M3', 'description' => 'Ordinateur portable Apple MacBook Air avec puce M3, 8Go RAM, 256Go SSD, écran Liquid Retina 13.6 pouces.', 'price' => '1299.00', 'stock' => 10, 'category' => 0],
            ['name' => 'Écouteurs Bluetooth Pro', 'description' => 'Écouteurs sans fil avec réduction de bruit active, autonomie 30h, étui de charge inclus.', 'price' => '79.99', 'stock' => 50, 'category' => 0],
            ['name' => 'Tablette iPad Air', 'description' => 'Tablette Apple iPad Air 11 pouces, puce M2, 128Go, Wi-Fi, compatible Apple Pencil.', 'price' => '699.00', 'stock' => 15, 'category' => 0],
            ['name' => 'T-shirt Premium Coton', 'description' => 'T-shirt en coton bio premium, coupe regular, disponible en plusieurs couleurs.', 'price' => '29.99', 'stock' => 100, 'category' => 1],
            ['name' => 'Jean Slim Fit', 'description' => 'Jean slim fit en denim stretch, coupe ajustée, lavage délavé moderne.', 'price' => '59.99', 'stock' => 60, 'category' => 1],
            ['name' => 'Veste en Cuir', 'description' => 'Veste en cuir véritable, doublure intérieure, style motard classique.', 'price' => '199.99', 'stock' => 20, 'category' => 1],
            ['name' => 'Lampe de Bureau LED', 'description' => 'Lampe de bureau LED avec variateur d intensité, bras articulé, port USB intégré.', 'price' => '45.99', 'stock' => 40, 'category' => 2],
            ['name' => 'Coussin Décoratif', 'description' => 'Coussin décoratif 45x45cm, housse en velours, rembourrage moelleux.', 'price' => '19.99', 'stock' => 80, 'category' => 2],
            ['name' => 'Tapis de Yoga', 'description' => 'Tapis de yoga antidérapant, épaisseur 6mm, matière écologique, sangle de transport incluse.', 'price' => '34.99', 'stock' => 45, 'category' => 3],
            ['name' => 'Haltères Réglables 20kg', 'description' => 'Paire d haltères réglables de 2 à 20kg, revêtement néoprène, support inclus.', 'price' => '89.99', 'stock' => 30, 'category' => 3],
            ['name' => 'Ballon de Football', 'description' => 'Ballon de football taille 5, cousu main, certifié FIFA Quality.', 'price' => '24.99', 'stock' => 55, 'category' => 3],
            ['name' => 'Le Petit Prince', 'description' => 'Le Petit Prince d Antoine de Saint-Exupéry, édition illustrée, couverture rigide.', 'price' => '12.99', 'stock' => 100, 'category' => 4],
            ['name' => 'Clean Code', 'description' => 'Clean Code: A Handbook of Agile Software Craftsmanship par Robert C. Martin. En anglais.', 'price' => '35.99', 'stock' => 25, 'category' => 4],
        ];

        foreach ($productsData as $prodData) {
            $product = new Product();
            $product->setName($prodData['name']);
            $product->setSlug($slugger->slug(strtolower($prodData['name']))->toString());
            $product->setDescription($prodData['description']);
            $product->setPrice($prodData['price']);
            $product->setStock($prodData['stock']);
            $product->setIsActive(true);
            $product->setCategory($categories[$prodData['category']]);
            $manager->persist($product);
        }

        $manager->flush();
    }
}
