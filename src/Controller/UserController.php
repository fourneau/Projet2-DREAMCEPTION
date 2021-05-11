<?php

namespace App\Controller;

use App\Model\UserManager;

/**
 * Class UserController
 *
 */
class UserController extends AbstractController
{

    /**
     * Display user listing
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function index()
    {
        if (isset($_SESSION['user']) && $_SESSION['user']['is_admin'] == 1) {
            $userManager = new UserManager();
            $users = $userManager->selectAll();

            return $this->twig->render('Admin/User/index.html.twig', ['users' => $users]);
        } else {
            header('Location:/');
        }
    }

    /**
     * Display product informations specified by $id
     *
     * @param int $id
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function show(int $id)
    {
        if (isset($_SESSION['user']) && $_SESSION['user']['is_admin'] == 1) {
            $userManager = new UserManager();
            $user = $userManager->selectOneById($id);

            return $this->twig->render('Admin/User/show.html.twig', ['user' => $user]);
        } else {
            header('Location:/');
        }
    }
    public function edit(int $id): string
    {
        if (isset($_SESSION['user']) && $_SESSION['user']['is_admin'] == 1) {
            $userManager = new UserManager();
            $user = $userManager->selectOneById($id);

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $user['username'] = $_POST['username'];
                $user['firstname'] = $_POST['firstname'];
                $user['lastname'] = $_POST['lastname'];
                $user['email'] = $_POST['email'];
                $user['password'] = $_POST['password'];
                $user['birthday'] = $_POST['birthday'];
                $user['adress'] = $_POST['adress'];
                $user['phone'] = $_POST['phone'];
                $user['is_admin'] = $_POST['is_admin'];
                $userManager->update($user);
                header('Location:/user/index');
            }

            return $this->twig->render('Admin/User/edit.html.twig', ['user' => $user]);
        } else {
            header('Location:/');
        }
    }
    /**
     * Display product creation page
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
            if (!empty($_POST['username']) && !empty($_POST['firstname']) && !empty($_POST['lastname']) && !empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['check_password']) && !empty($_POST['birthday']) && !empty($_POST['adress']) && !empty($_POST['phone']) && !empty($_POST['is_admin'])) {
                $user = $userManager->searchUser($_POST['username']);
                if (!$user) {
                    if ($_POST['password'] === $_POST['check_password']) {
                        if (strlen($_POST['password']) >= 6 && strlen($_POST['password']) <= 10) {
                            $user = [
                                'username' => $_POST['username'],
                                'firstname' => $_POST['firstname'],
                                'lastname' => $_POST['lastname'],
                                'email' => $_POST['email'],
                                'password' => md5($_POST['password']),
                                'birthday' => $_POST['birthday'],
                                'adress' => $_POST['adress'],
                                'phone' => $_POST['phone'],
                                'is_admin' => $_POST['is_admin'],
                            ];
                            $id = $userManager->insert($user);
                            if ($id) {
                                $_SESSION['user'] = $userManager->selectOneById($id);
                                header('Location:/user/index');
                            }
                        } else {
                            $errors[] = "Le mot de passe doit contenir entre 6 et 10 caractères!";
                        }
                    } else {
                        $errors[] = "Les mots de passe ne correspondent pas !";
                    }
                } else {
                    $errors[] = "Ce pseudo existe déjà.";
                }
            } else {
                $errors[] = "Tous les champs sont requis";
            }
        }
        return $this->twig->render('Admin/User/register.html.twig', [
            'user' => $userManager->selectAll(), 'errors' => $errors
        ]);
    }


    /**
     * Handle product deletion
     *
     * @param int $id
     */
    public function delete(int $id)
    {
        if (isset($_SESSION['user']) && $_SESSION['user']['is_admin'] == 1) {
            $userManager = new UserManager();
            $userManager->delete($id);
            header('Location:/user/index');
        } else {
            header('Location:/');
        }
    }
}
