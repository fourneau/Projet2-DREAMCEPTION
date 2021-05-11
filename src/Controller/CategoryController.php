<?php

/**
 * Created by PhpStorm.
 * User: root
 * Date: 11/10/17
 * Time: 16:07
 * PHP version 7
 */

namespace App\Controller;

use App\Model\CategoryManager;
use App\Model\ProductManager;

/**
 * Class CategoryController
 *
 */
class CategoryController extends AbstractController
{
    public function index()
    {
        $categoryManager = new CategoryManager();
        $categories = $categoryManager->selectAll();

        return $this->twig->render('Admin/Category/index.html.twig', ['categories' => $categories]);
    }
    /**
     * Display category informations specified by $id
     *
     * @param int $id
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function show(int $id)
    {
        $categoryManager = new categoryManager();
        $category = $categoryManager->selectOneById($id);
        $productManager = new productManager();
        $products = $productManager->selectAllbyCat();
        return $this->twig->render('Admin/Category/show.html.twig', ['category' => $category, 'products' => $products]);
    }
}
