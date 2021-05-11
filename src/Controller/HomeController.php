<?php

/**
 * Created by PhpStorm.
 * User: aurelwcs
 * Date: 08/04/19
 * Time: 18:40
 */

namespace App\Controller;

use App\Model\ProductManager;
use App\Model\UserManager;
use App\Model\OrderManager;
use App\Model\OrderProductManager;
use App\Model\MovieReservationManager;

class HomeController extends AbstractController
{
    /**
     * Display home page
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function index()
    {
        $userManager = new UserManager();
        return $this->twig->render('Home/index.html.twig', ['user' => $userManager->selectAll()]);
    }

    public function cart()
    {
        $cartInfos = $this->getCartInfos();
        $totalInCart = $this->totalInCart();
        return $this->twig->render('Home/cart.html.twig', ['cart' => $cartInfos, 'totalInCart' => $totalInCart]);
    }

    public function getCartInfos()
    {
        if (isset($_SESSION['cart'])) {
            $cart = $_SESSION['cart'];
            $cartInfos = [];
            $productManager = new ProductManager();
            foreach ($cart as $idProduct => $qty) {
                $product = $productManager->selectOneById($idProduct);
                $product['qty'] = $qty;
                $cartInfos[] = $product;
            }
            return $cartInfos;
        }
        return false;
    }

    public function addToCart(int $idProduct)
    {
        $productManager = new ProductManager();
        $availableQty = $productManager->selectOneById($idProduct)['stock'];
        if (!isset($_SESSION['cart'][$idProduct])) {
            $_SESSION['cart'][$idProduct] = 0;
        }
        if ($_SESSION['cart'][$idProduct] >= $availableQty) {
            return $this->twig->render('Home/errorcart.html.twig');
        }
        $_SESSION['cart'][$idProduct]++;

        header('Location: /home/cart');
    }

    public function addInCart()
    {
        $productManager = new ProductManager();
        $newCart = [];
        $_SESSION['cart'][$_POST['id']] = $_POST['qty'];
        $_POST['id'] = $_POST['id'];
        $_POST['qty'] = $_POST['qty'];
        $availableQty = $productManager->selectOneById($_POST['id'])['stock'];
        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            if ($_SESSION['cart'][$_POST['id']] <= $availableQty) {
                $newCart['post'] = $_POST;
                $newCart['name'] = $productManager->selectOneById($_POST['id'])['name'];
                $newCart['total'] = $this->totalInCart();
                header('Location: /home/cart');
            } else {
                unset($_SESSION['cart'][$_POST['id']]);
                return $this->twig->render('Home/errorcart.html.twig');
            }
        }
    }

    public function deleteFromCart(int $idProduct)
    {
        $cart = $_SESSION['cart'];
        if (!empty($cart[$idProduct])) {
            unset($cart[$idProduct]);
        }
        $_SESSION['cart'] = $cart;
        header('Location: /home/cart');
    }

    public function totalInCart()
    {
        $total = 0;
        if ($this->getCartInfos() != false) {
            foreach ($this->getCartInfos() as $product) {
                $total += $product['price'] * intval($product['qty']);
            }
            return $total;
        }
        return $total;
    }
    public function order()
    {
        $orderManager = new OrderManager();
        $orderProductManager = new OrderProductManager();
        $productManager = new ProductManager();
        if (!empty($_SESSION['cart'])) {
            if ($_SERVER['REQUEST_METHOD'] === "POST") {
                if (!empty($_POST['address'])) {
                    $order = [
                        'order_date' => date('y-m-d'),
                        'total' => $this->totalInCart(),
                        'user_id' => $_SESSION['user']['id'],
                    ];
                    $idOrder = $orderManager->insert($order);

                    if ($idOrder) {
                        foreach ($_SESSION['cart'] as $idProduct => $qty) {
                            $product = $productManager->selectOneById($idProduct);
                            $newQty = $product['stock'] - $qty;
                            $productManager->updateQty($idProduct, $newQty);
                            $newLineInTickets = [
                                'order_id' => $idOrder,
                                'product_id' => $idProduct,
                                'qty' => $qty,
                                'address' => $_POST['address']
                            ];
                            $orderProductManager->insert($newLineInTickets);
                        }
                        unset($_SESSION['cart']);
                        header('Location: /home/orderDetail/' . $idOrder);
                    }
                }
            }
            return $this->twig->render('Home/order.html.twig', ['session', $_SESSION['user']]);
        } else {
            header('Location:/');
        }
    }

    public function orderDetail(int $orderId)
    {
        $orderProductManager = new OrderProductManager();
        $productManager = new ProductManager();
        $orderManager = new OrderManager();

        $ticket = $orderProductManager->getTicketFromOrderId($orderId);

        $result = [];
        foreach ($ticket as $detail) {
            $product = $productManager->selectOneById($detail['product_id']);
            $detail['product_id'] = $product;
            $result[] = $detail;
        }
        foreach ($ticket as $orderDate) {
            $orderDate = $orderManager->getDateFromOrder($orderId);
        }
        return $this->twig->render('Home/orderdetail.html.twig', [
            'ticket' => $result,
            'date' => $orderDate[$orderId - 1],
            'orderProduct' => $ticket,
            'orderId' => $orderId
        ]);
    }

    public function showProduct(int $id)
    {
        $productManager = new ProductManager();
        $product = $productManager->selectOneById($id);
        return $this->twig->render('Home/showproduct.html.twig', ['product' => $product]);
    }
}
