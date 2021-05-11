<?php

namespace App\Controller;

use App\Model\ContactManager;
use OndraM\CiDetector\Ci\GitLab;

class ContactController extends AbstractController
{



    /**
     * Display contact page
     */

    public function index()
    {
        $contactManager = new ContactManager();
        $contacts = $contactManager->selectAll();
        return $this->twig->render('Admin/Contact/index.html.twig', ['contacts' => $contacts]);
    }
    public function show(int $id)
    {
        $contactManager = new ContactManager();
        $contact = $contactManager->selectOneById($id);
        return $this->twig->render('Admin/Contact/show.html.twig', ['contact' => $contact]);
    }
    public function new()
    {
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (empty($_POST['firstname'])) {
                $errors['firstname'] = "Veuillez renseigner un nom";
            }
            if (empty($_POST['lastname'])) {
                $errors['lastname'] = "Veuillez renseigner un prénom";
            }
            if (empty($_POST['subject'])) {
                $errors['subject'] = "Veuillez renseigner un sujet";
            }
            if (empty($_POST['email'])) {
                $errors['email'] = "Veuillez renseigner une adresse email valide";
            }
            if (empty($_POST['message'])) {
                $errors['message'] = "Veuillez détailler votre demande";
            }
            if (empty($errors)) {
                $contactManager = new ContactManager();
                $contact = [
                    'firstname' => $_POST['firstname'],
                    'lastname' => $_POST['lastname'],
                    'email' => $_POST['email'],
                    'subject' => $_POST['subject'],
                    'message' => $_POST['message'],
                ];
                $contactManager->insert($contact);
                header('Location:/contact/success');
            }
        }
        return $this->twig->render('Home/contact.html.twig', ['errors' => $errors]);
    }
    public function success()
    {
        return $this->twig->render('Home/success.html.twig');
    }
    /**
     * Handle contact deletion
     *
     * @param int $id
     */
    public function delete(int $id)
    {
        $contactManager = new ContactManager();
        $contactManager->delete($id);
        header('Location:/contact/index');
    }

    public function newFormUser()
    {
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (empty($_POST['firstname'])) {
                $errors['firstname'] = "Veuillez renseigner un nom";
            }
            if (empty($_POST['lastname'])) {
                $errors['lastname'] = "Veuillez renseigner un prénom";
            }
            if (empty($_POST['subject'])) {
                $errors['subject'] = "Veuillez renseigner un sujet";
            }
            if (empty($_POST['email'])) {
                $errors['email'] = "Veuillez renseigner une adresse email valide";
            }
            if (empty($_POST['message'])) {
                $errors['message'] = "Veuillez détailler votre demande";
            }
            if (empty($errors)) {
                $contactManager = new ContactManager();
                $contact = [
                    'firstname' => $_POST['firstname'],
                    'lastname' => $_POST['lastname'],
                    'email' => $_POST['email'],
                    'subject' => $_POST['subject'],
                    'message' => $_POST['message'],
                ];
                $contactManager->insert($contact);
                header('Location:/contact/success');
            }
        }
        return $this->twig->render('Home/form_user.html.twig', ['errors' => $errors]);
    }
}
