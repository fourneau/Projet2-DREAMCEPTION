<?php

/**
 * Created by PhpStorm.
 * User: aurelwcs
 * Date: 08/04/19
 * Time: 18:40
 */

namespace App\Controller;

use App\Model\UserManager;
use App\Model\OrderManager;

class SecurityController extends AbstractController
{
    /**
     * Display home page
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */

    public function register()
    {
        $userManager = new UserManager();
        $errors = [];
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
            if ($fileSize > 100000) {
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
                    'is_admin' => 0,
                ];
                $id = $userManager->insert($user);
                if ($id) {
                    $_SESSION['user'] = $userManager->selectOneById($id);
                    header('Location: /');
                } else {
                    header('Location: /');
                }
            }
        }

        return $this->twig->render(
            'Security/register.html.twig',
            [
                'user' => $userManager->selectAll(),
                'errors' => $errors
            ]
        );
    }

    public function login()
    {
        $userManager = new UserManager();
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            if (empty($_POST['username'])) {
                $errors['username'] = "Votre pseudo est requis";
            }

            if (empty($_POST['password'])) {
                $errors['password'] = "Un mot de passe est requis";
            }
            if (!empty($_POST['username']) && !empty($_POST['password'])) {
                $user = $userManager->searchUser($_POST['username']);
                if ($user) {
                    if ($user['password'] === md5($_POST['password'])) {
                        if ($user['is_admin'] === '1') {
                            $_SESSION['user'] = $user;
                            header('Location: /admin/index');
                        } else {
                            $_SESSION['user'] = $user;
                            header('Location: /');
                        }
                    } else {
                        $errors['password1'] = "Mot de passe invalide";
                    }
                } else {
                    $errors['username1'] = "Ce pseudo n'existe pas !";
                }
            }
        }
        return $this->twig->render(
            'Security/login.html.twig',
            [
                'user' => $userManager->selectAll(),
                'errors' => $errors
            ]
        );
    }

    public function logout()
    {
        session_destroy();
        header('Location: /');
    }

    public function show()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: /');
        }
        $orderManager = new OrderManager();
        $orders = $orderManager->getOrdersByUser($_SESSION['user']['id']);
        return $this->twig->render('Security/show.html.twig', ['orders' => $orders]);
    }

    public function edit(int $id)
    {
        if (isset($_SESSION['user']) && $_SESSION['user']['is_admin'] == 0) {
            $errors = [];
            $userManager = new UserManager();
            $user = $userManager->selectOneById($id);

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

                if (empty($_POST['username'])) {
                    $errors['username'] = "Votre pseudo est requis";
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
                if ($fileSize > 500000) {
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
                    $user['username'] = $_POST['username'];
                    $user['firstname'] = $_POST['firstname'];
                    $user['lastname'] = $_POST['lastname'];
                    $user['email'] = $_POST['email'];
                    $user['password'] = md5($_POST['password']);
                    $user['birthday'] = $_POST['birthday'];
                    $user['adress'] = $_POST['adress'];
                    $user['phone'] = $_POST['phone'];
                    $user['image'] = $assetDir;
                    $user['is_admin'] = 0;
                    $user = $userManager->update($user);
                    unset($_SESSION['user']);
                    $_SESSION['user'] = $userManager->selectOneById($id);
                    header('Location: /security/show');
                }
            }

            return $this->twig->render('Security/edit.html.twig', ['user' => $user, 'errors' => $errors]);
        } else {
            header('Location: /');
        }
    }
}
