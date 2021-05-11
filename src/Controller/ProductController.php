<?php

namespace App\Controller;

use App\Model\ProductManager;
use App\Model\CategoryManager;

class ProductController extends AbstractController
{

    public function indexUser()
    {
        $categoryManager = new categoryManager();
        $categories = $categoryManager->selectAll();
        $productManager = new ProductManager();
        $products = $productManager->selectAll();

        return $this->twig->render('Home/products.html.twig', ['products' => $products, 'categories' => $categories]);
    }

    public function filterCategory(int $id)
    {
        $categoryManager = new categoryManager();
        $category = $categoryManager->selectOneById($id);
        $categories = $categoryManager->selectAll();
        $productManager = new ProductManager();
        $products = $productManager->selectAllbyCat();

        return $this->twig->render(
            'Home/products.html.twig',
            ['products' => $products, 'category' => $category, 'categories' => $categories]
        );
    }
    public function filterExPrice()
    {
        $categoryManager = new categoryManager();
        $categories = $categoryManager->selectAll();
        $productManager = new ProductManager();
        $products = $productManager->selectAllbyExPrice();

        return $this->twig->render('Home/products.html.twig', ['products' => $products, 'categories' => $categories]);
    }
    public function filterChPrice()
    {
        $categoryManager = new categoryManager();
        $categories = $categoryManager->selectAll();
        $productManager = new ProductManager();
        $products = $productManager->selectAllbyChPrice();

        return $this->twig->render('Home/products.html.twig', ['products' => $products, 'categories' => $categories]);
    }
}
