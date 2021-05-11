<?php

namespace App\Controller;

use App\Model\ContactManager;
use App\Model\MovieReservationManager;
use App\Model\OrderManager;
use App\Model\UserManager;
use App\Model\ProductManager;
use App\Model\CategoryManager;
use App\Model\MovieManager;
use App\Model\OrderProductManager;

class AdminController extends AbstractController
{
    public function index()
    {
        if (isset($_SESSION['user']) && $_SESSION['user']['is_admin'] == 1) {
            $userManager = new UserManager();
            $contactManager = new ContactManager();
            $orderManager = new OrderManager();
            $movieReservation = new MovieReservationManager();
            $tickets = $contactManager->getAllTickets();
            $users = $userManager->getAllUsers();
            $userInfo = $userManager->selectAll();
            $orders = $orderManager->getAllOrders();
            $sales = $orderManager->getTotals();
            $order = $orderManager->selectAllForOrders();
            $orderAdmin = $orderManager->selectAllForOrders();
            $productManager = new ProductManager();
            $dashBoardProducts = $productManager->selectAll();
            $dashBoardMovieReservation = $movieReservation->selectAll();

            return $this->twig->render(
                'Admin/index.html.twig',
                [
                    'dashBoardProducts' => $dashBoardProducts,
                    'order' => $order,
                    'sales' => $sales,
                    'orders' => $orders,
                    'tickets' => $tickets,
                    'users' => $users,
                    'userInfo' => array_reverse($userInfo),
                    'dashBoardMovieReservation' => array_reverse($dashBoardMovieReservation),
                    'orderAdmin' => array_reverse($orderAdmin)
                ]
            );
        } else {
            header('Location: /');
        }
    }

    /**
     *
     * PRODUCT PART !
     *
     * */

    public function indexProduct()
    {
        if (isset($_SESSION['user']) && $_SESSION['user']['is_admin'] == 1) {
            $productManager = new ProductManager();
            $products = $productManager->selectAll();

            return $this->twig->render('Admin/Product/index.html.twig', ['products' => $products]);
        } else {
            header('Location: /');
        }
    }

    public function showProduct(int $id)
    {
        if (isset($_SESSION['user']) && $_SESSION['user']['is_admin'] == 1) {
            $productManager = new ProductManager();
            $product = $productManager->selectOneById($id);

            return $this->twig->render('Admin/Product/show.html.twig', ['product' => $product]);
        } else {
            header('Location: /');
        }
    }

    public function addProduct()
    {
        $productManager = new ProductManager();
        $categoryManager = new CategoryManager();
        $errors = [];

        if (!isset($_SESSION['user']) && $_SESSION['user']['is_admin'] == 1) {
            $errors = "Session not admin";
            header('Location: /');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $file = $_FILES['image'];
            $fileName = $_FILES['image']['name'];
            $fileTmpName = $_FILES['image']['tmp_name'];
            $fileSize = $_FILES['image']['size'];
            $fileError = $_FILES['image']['error'];
            $fileType = $_FILES['image']['type'];
            $fileExt = explode('.', $fileName);
            $fileActualExt = strtolower(end($fileExt));
            $allowed = array('jpg', 'jpeg', 'png');

            if (empty($_POST['name'])) {
                $errors['name'] = "Le nom du produit est requis";
            }
            if (empty($_POST['price'])) {
                $errors['price'] = "Le price du produit est requis";
            }

            if (empty($_POST['stock'])) {
                $errors['stock'] = "Le stock du produit est requis";
            }

            if (empty($_POST['description'])) {
                $errors['description'] = "La description du produit est requis";
            }

            if (empty($_POST['weight'])) {
                $errors['weight'] = "Le poids du produit est requis";
            }

            if ($_POST['category_id'] <= 0) {
                $errors['category_id'] = "La catégorie du produit est requise";
            }

            if (!in_array($fileActualExt, $allowed)) {
                $errors['image'] = "You cannot upload files of this type";
            }
            if ($fileError === 1) {
                $errors['image'] = "there was an error uploading your file";
            }
            if ($fileSize > 1000000) {
                $errors['image'] = "Your file is too big";
            }

            if (empty($errors)) {
                $fileNameNew = uniqid('', true) . "." . $fileActualExt;
                $uploadDir = '../public/assets/upload/' . $fileNameNew;
                $assetDir = '../assets/upload/' . $fileNameNew;
                if (!move_uploaded_file($fileTmpName, $uploadDir)) {
                    $errors['image'] = "Upload File Failed";
                }
            }

            if (empty($errors)) {
                $product = [
                    'name' => $_POST['name'],
                    'price' => $_POST['price'],
                    'stock' => $_POST['stock'],
                    'description' => $_POST['description'],
                    'image' => $assetDir,
                    'weight' => $_POST['weight'],
                    'category_id' => $_POST['category_id'],
                ];
                $productManager->insert($product);
                header('Location:/admin/indexProduct');
            }
        }

        return $this->twig->render(
            'Admin/Product/add.html.twig',
            [
                'categories' => $categoryManager->selectAll(),
                'errors' => $errors,

            ]
        );
    }

    public function editProduct(int $id)
    {
        if (isset($_SESSION['user']) && $_SESSION['user']['is_admin'] == 1) {
            $categoryManager = new CategoryManager();
            $productManager = new ProductManager();
            $product = $productManager->selectOneById($id);

            $errors = [];
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $file = $_FILES['image'];
                $fileName = $_FILES['image']['name'];
                $fileTmpName = $_FILES['image']['tmp_name'];
                $fileSize = $_FILES['image']['size'];
                $fileError = $_FILES['image']['error'];
                $fileType = $_FILES['image']['type'];
                $fileExt = explode('.', $fileName);
                $fileActualExt = strtolower(end($fileExt));
                $allowed = array('jpg', 'jpeg', 'png');

                if (empty($_POST['name'])) {
                    $errors['name'] = "Le nom du produit est requis";
                }

                if (empty($_POST['price'])) {
                    $errors['price'] = "Le price du produit est requis";
                }

                if (empty($_POST['stock'])) {
                    $errors['stock'] = "Le stock du produit est requis";
                }

                if (empty($_POST['description'])) {
                    $errors['description'] = "La description du produit est requis";
                }

                if (empty($_POST['weight'])) {
                    $errors['weight'] = "Le poids du produit est requis";
                }

                if (empty($_POST['category_id'])) {
                    $errors['category_id'] = "La catégorie du produit est requise";
                }
                if (!in_array($fileActualExt, $allowed)) {
                    $errors['image'] = "You cannot upload files of this type";
                }
                if ($fileError === 1) {
                    $errors['image'] = "there was an error uploading your file";
                }
                if ($fileSize > 1000000) {
                    $errors['image'] = "Your file is too big";
                }
                if (empty($errors)) {
                    $fileNameNew = uniqid('', true) . "." . $fileActualExt;
                    $uploadDir = '../public/assets/upload/' . $fileNameNew;
                    $assetDir = '../assets/upload/' . $fileNameNew;
                    if (!move_uploaded_file($fileTmpName, $uploadDir)) {
                        $errors['image'] = "Upload File Failed";
                    }
                }

                if (empty($errors)) {
                    $product['name'] = $_POST['name'];
                    $product['price'] = $_POST['price'];
                    $product['stock'] = $_POST['stock'];
                    $product['description'] = $_POST['description'];
                    $product['image'] = $assetDir;
                    $product['weight'] = $_POST['weight'];
                    $product['category_id'] = $_POST['category_id'];
                    $productManager->update($product);
                    header('Location:/admin/indexProduct');
                }
            }
            return $this->twig->render(
                'Admin/Product/edit.html.twig',
                [
                    'product' => $product,
                    'categories' => $categoryManager->selectAll(),
                    'errors' => $errors
                ]
            );
        } else {
            header('Location: /');
        }
    }

    public function deleteProduct(int $id)
    {
        if (isset($_SESSION['user']) && $_SESSION['user']['is_admin'] == 1) {
            $productManager = new ProductManager();
            $delete = 1;
            $productManager->softdelete($id, $delete);
            header('Location:/admin/indexProduct');
        } else {
            header('Location: /');
        }
    }


    /**
     *
     * CATEGORY PART !
     *
     */

    public function indexCategory()
    {
        if (isset($_SESSION['user']) && $_SESSION['user']['is_admin'] == 1) {
            $categoryManager = new CategoryManager();
            $categories = $categoryManager->selectAll();

            return $this->twig->render('Admin/Category/index.html.twig', ['categories' => $categories]);
        } else {
            header('Location: /');
        }
    }

    public function showCategory(int $id)
    {
        if (isset($_SESSION['user']) && $_SESSION['user']['is_admin'] == 1) {
            $categoryManager = new categoryManager();
            $category = $categoryManager->selectOneById($id);
            $productManager = new productManager();
            $products = $productManager->selectAllbyCat();
            return $this->twig->render(
                'Admin/Category/show.html.twig',
                [
                    'category' => $category,
                    'products' => $products
                ]
            );
        } else {
            header('Location: /');
        }
    }

    public function editCategory(int $id)
    {
        if (isset($_SESSION['user']) && $_SESSION['user']['is_admin'] == 1) {
            $categoryManager = new CategoryManager();
            $category = $categoryManager->selectOneById($id);
            $errors = [];
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (!empty($_POST['name'])) {
                    $category['name'] = $_POST['name'];
                    $categoryManager->update($category);
                    header('Location:/admin/indexCategory');
                } else {
                    $errors[] = "Le nom de la catégorie est requise";
                }
            }

            return $this->twig->render('Admin/Category/edit.html.twig', ['category' => $category, 'errors' => $errors]);
        } else {
            header('Location: /');
        }
    }

    public function addCategory()
    {
        if (isset($_SESSION['user']) && $_SESSION['user']['is_admin'] == 1) {
            $categoryManager = new CategoryManager();
            $errors = [];
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (!empty($_POST['name'])) {
                    $category = [
                        'name' => $_POST['name'],
                    ];
                    $id = $categoryManager->insert($category);
                    header('Location:/admin/showCategory/' . $id);
                } else {
                    $errors[] = "Le nom de la catégorie est requise";
                }
            }

            return $this->twig->render('Admin/Category/add.html.twig', ['errors' => $errors]);
        } else {
            header('Location: /');
        }
    }

    public function deleteCat(int $id)
    {
        if (isset($_SESSION['user']) && $_SESSION['user']['is_admin'] == 1) {
            $categoryManager = new CategoryManager();
            $productManager = new ProductManager();
            $orderProductManager = new OrderProductManager();
            $products = $productManager->selectAllbyCat();
            foreach ($products as $product) {
                if ($product['category_id'] == $id) {
                    $productId = $product['id'];
                    $productManager->delete($productId);
                }
            }
            $categoryManager->delete($id);
            header('Location:/admin/indexCategory');
        } else {
            header('Location: /');
        }
    }

    public function deleteCategory(int $id)
    {
        if (isset($_SESSION['user']) && $_SESSION['user']['is_admin'] == 1) {
            $categoryManager = new CategoryManager();
            $delete = 1;
            $categoryManager->softdelete($id, $delete);
            header('Location:/admin/indexCategory');
        } else {
            header('Location: /');
        }
    }

    /**
     *
     * MOVIE PART !
     *
     **/
    public function indexMovies()
    {
        if (isset($_SESSION['user']) && $_SESSION['user']['is_admin'] == 1) {
            $movieManager = new MovieManager();
            $movies = $movieManager->selectAll();

            return $this->twig->render('Admin/Movie/index.html.twig', ['movies' => $movies]);
        } else {
            header('Location: /');
        }
    }

    public function showMovie(int $id)
    {
        if (isset($_SESSION['user']) && $_SESSION['user']['is_admin'] == 1) {
            $movieManager = new MovieManager();
            $movie = $movieManager->selectOneById($id);

            return $this->twig->render('Admin/Movie/show.html.twig', ['movie' => $movie]);
        } else {
            header('Location: /');
        }
    }

    public function editMovie(int $id)
    {
        if (isset($_SESSION['user']) && $_SESSION['user']['is_admin'] == 1) {
            $movieManager = new MovieManager();
            $movie = $movieManager->selectOneById($id);
            $errors = [];
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $file = $_FILES['image'];
                $fileName = $_FILES['image']['name'];
                $fileTmpName = $_FILES['image']['tmp_name'];
                $fileSize = $_FILES['image']['size'];
                $fileError = $_FILES['image']['error'];
                $fileType = $_FILES['image']['type'];

                $fileExt = explode('.', $fileName);
                $fileActualExt = strtolower(end($fileExt));

                $allowed = array('jpg', 'jpeg', 'png');

                if (empty($_POST['name'])) {
                    $errors['name'] = "Le nom du film est requis";
                }

                if (empty($_POST['minutes'])) {
                    $errors['minutes'] = "La durée du film est requise";
                }

                if (empty($_POST['description'])) {
                    $errors['description'] = "La description du film est requise";
                }

                if (empty($_POST['price'])) {
                    $errors['price'] = "Le prix de la séance est requis";
                }
                if (!in_array($fileActualExt, $allowed)) {
                    $errors['image'] = "You cannot upload files of this type";
                }
                if ($fileError === 1) {
                    $errors['image'] = "there was an error uploading your file";
                }
                if ($fileSize > 1000000) {
                    $errors['image'] = "Your file is too big";
                }
                if (empty($errors)) {
                    $fileNameNew = uniqid('', true) . "." . $fileActualExt;
                    $uploadDir = '../public/assets/upload/movies/' . $fileNameNew;
                    $assetDir = '../assets/upload/movies/' . $fileNameNew;
                    if (!move_uploaded_file($fileTmpName, $uploadDir)) {
                        $errors['image'] = "Upload File Failed";
                    }
                }
                if (empty($errors)) {
                    $movie['name'] = $_POST['name'];
                    $movie['minutes'] = $_POST['minutes'];
                    $movie['description'] = $_POST['description'];
                    $movie['price'] = $_POST['price'];
                    $movie['image'] = $assetDir;
                    $movieManager->update($movie);
                    header('Location:/admin/indexMovies');
                }
            }
            return $this->twig->render('Admin/Movie/edit.html.twig', ['movie' => $movie, 'errors' => $errors]);
        } else {
            header('Location: /');
        }
    }

    public function addMovie()
    {
        $movieManager = new MovieManager();
        $errors = [];
        if (!isset($_SESSION['user']) && $_SESSION['user']['is_admin'] == 1) {
            $errors = "Session not admin";
            header('Location: /');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $file = $_FILES['image'];
            $fileName = $_FILES['image']['name'];
            $fileTmpName = $_FILES['image']['tmp_name'];
            $fileSize = $_FILES['image']['size'];
            $fileError = $_FILES['image']['error'];
            $fileType = $_FILES['image']['type'];

            $fileExt = explode('.', $fileName);
            $fileActualExt = strtolower(end($fileExt));

            $allowed = array('jpg', 'jpeg', 'png');

            if (empty($_POST['name'])) {
                $errors['name'] = "Le nom du film est requis";
            }

            if (empty($_POST['minutes'])) {
                $errors['minutes'] = "La durée du film est requise";
            }

            if (empty($_POST['description'])) {
                $errors['description'] = "La description du film est requise";
            }

            if (empty($_POST['price'])) {
                $errors['price'] = "Le prix de la séance est requis";
            }
            if (!in_array($fileActualExt, $allowed)) {
                $errors['image'] = "You cannot upload files of this type";
            }
            if ($fileError === 1) {
                $errors['image'] = "there was an error uploading your file";
            }
            if ($fileSize > 1000000) {
                $errors['image'] = "Your file is too big";
            }

            if (empty($errors)) {
                $fileNameNew = uniqid('', true) . "." . $fileActualExt;
                $uploadDir = '../public/assets/upload/movies/' . $fileNameNew;
                $assetDir = '../assets/upload/movies/' . $fileNameNew;
                if (!move_uploaded_file($fileTmpName, $uploadDir)) {
                    $errors['image'] = "Upload File Failed";
                }
            }
            if (empty($errors)) {
                $movie = [
                    'name' => $_POST['name'],
                    'minutes' => $_POST['minutes'],
                    'description' => $_POST['description'],
                    'price' => $_POST['price'],
                    'image' => $assetDir
                ];
                $id = $movieManager->insert($movie);
                header('Location:/admin/showMovie/' . $id);
            }
        }
        return $this->twig->render('Admin/Movie/add.html.twig', ['errors' => $errors]);
    }

    public function deleteMovie(int $id)
    {
        if (isset($_SESSION['user']) && $_SESSION['user']['is_admin'] == 1) {
            $movieManager = new MovieManager();
            $movieManager->delete($id);
            header('Location:/admin/indexMovies');
        } else {
            header('Location: /');
        }
    }

    public function register()
    {
        $userManager = new UserManager();
        $errors = [];
        if (!isset($_SESSION['user']) && $_SESSION['user']['is_admin'] == 1) {
            $errors = "Session not admin";
            header('Location: /');
        }
        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $file = $_FILES['image'];
            $fileName = $_FILES['image']['name'];
            $fileTmpName = $_FILES['image']['tmp_name'];
            $fileSize = $_FILES['image']['size'];
            $fileError = $_FILES['image']['error'];
            $fileType = $_FILES['image']['type'];

            $fileExt = explode('.', $fileName);
            $fileActualExt = strtolower(end($fileExt));

            $allowed = array('jpg', 'jpeg', 'png');

            if (!empty($_POST['username'])) {
                $user = $userManager->searchUser($_POST['username']);
                if ($user) {
                    $errors['username'] = "Ce pseudo existe déjà.";
                }
            } else {
                $errors['username'] = "Username required.";
            }
            if (empty($_POST['firstname'])) {
                $errors['firstname'] = "Votre prénom est requis";
            }
            if (empty($_POST['lastname'])) {
                $errors['lastname'] = "Votre nom est requis";
            }
            if (empty($_POST['email'])) {
                $errors['email'] = "Votre email est requis";
            }
            if (!empty($_POST['password'])) {
                if (strlen($_POST['password']) < 6 || strlen($_POST['password']) > 10) {
                    $errors['password'] = "Le mot de passe doit contenir entre 6 et 10 caractères!";
                }
            } else {
                $errors['password'] = "Un mot de passe est requis";
            }
            if (!empty($_POST['check_password'])) {
                if ($_POST['password'] != $_POST['check_password']) {
                    $errors['password'] = "Les mots de passe ne correspondent pas !";
                }
            } else {
                $errors['check_password'] = "Veuillez entrer une nouvelle fois votre mot de passe";
            }

            if (empty($_POST['birthday'])) {
                $errors['birthday'] = "Votre date de naissance est requise";
            }
            if (empty($_POST['adress'])) {
                $errors['adress'] = "Votre adresse est requise";
            }
            if (empty($_POST['phone'])) {
                $errors['phone'] = "Votre numéro de téléphone est requis";
            }
            if (!in_array($fileActualExt, $allowed)) {
                $errors['image'] = "You cannot upload files of this type";
            }
            if ($fileError === 1) {
                $errors['image'] = "there was an error uploading your file";
            }
            if ($fileSize > 1000000) {
                $errors['image'] = "Your file is too big";
            }

            if (empty($errors)) {
                $fileNameNew = uniqid('', true) . "." . $fileActualExt;
                $uploadDir = '../public/assets/upload/' . $fileNameNew;
                $assetDir = '../assets/upload/' . $fileNameNew;
                if (!move_uploaded_file($fileTmpName, $uploadDir)) {
                    $errors['image'] = "Upload File Failed";
                }
            }
            if (empty($errors)) {
                $user = [
                    'username' => $_POST['username'],
                    'firstname' => $_POST['firstname'],
                    'lastname' => $_POST['lastname'],
                    'email' => $_POST['email'],
                    'password' => md5($_POST['password']),
                    'birthday' => $_POST['birthday'],
                    'adress' => $_POST['adress'],
                    'phone' => $_POST['phone'],
                    'image' => $assetDir,
                    'is_admin' => 1
                ];
                $id = $userManager->insert($user);
                if ($id) {
                    $_SESSION['user'] = $userManager->selectOneById($id);
                    header('Location:/user/index');
                }
            }
        }
        return $this->twig->render(
            'Admin/User/register.html.twig',
            [
                'user' => $userManager->selectAll(),
                'errors' => $errors,
            ]
        );
    }

    public function indexUsers()
    {
        if (isset($_SESSION['user']) && $_SESSION['user']['is_admin'] == 1) {
            $userManager = new UserManager();
            $users = $userManager->selectAll();

            return $this->twig->render(
                'Admin/User/index.html.twig',
                ['users' => $users]
            );
        } else {
            header('Location: /');
        }
    }


    public function indexOrders()
    {
        if (isset($_SESSION['user']) && $_SESSION['user']['is_admin'] == 1) {
            $orderManager = new OrderManager();
            $indexOrders = $orderManager->selectAllForOrders();
            $sales = $orderManager->getTotals();

            return $this->twig->render(
                'Admin/Order/index.html.twig',
                ['sales' => $sales, 'indexOrders' => $indexOrders]
            );
        } else {
            header('Location: /');
        }
    }


    public function orderDetailAdmin(
        int $orderId
    ) {
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
        return $this->twig->render(
            'Admin/Order/show.html.twig',
            [
                'ticket' => $result,
                'date' => $orderDate[$orderId - 1],
                'orderProduct' => $ticket,
                'orderId' => $orderId
            ]
        );
    }

    public function indexMovieReservation()
    {
        if (isset($_SESSION['user']) && $_SESSION['user']['is_admin'] == 1) {
            $movieReservationManager = new MovieReservationManager();
            $movieReservations = $movieReservationManager->selectAll();

            return $this->twig->render(
                'Admin/MovieReservation/index.html.twig',
                ['movieReservations' => $movieReservations]
            );
        } else {
            header('Location: /');
        }
    }
}
