<?php

/**
 * Created by PhpStorm.
 * User: aurelwcs
 * Date: 08/04/19
 * Time: 18:40
 */

namespace App\Controller;

use App\Model\MovieReservationManager;

class MovieReservationController extends AbstractController
{

    public function addMovieReservation(int $idMovie)
    {
        if (isset($_SESSION['user'])) {
            $movieReservationManager = new MovieReservationManager();
            $errors = [];

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (empty($_POST['date'])) {
                    $errors['date'] = "La date est requise";
                }

                if (empty($_POST['nb_pers'])) {
                    $errors['nb_pers'] = "Le nombre de personne est requis";
                }

                if (empty($_POST['time'])) {
                    $errors['time'] = "L'heure est requise";
                }

                if (empty($errors)) {
                    $movieReservation = [
                        'date' => $_POST['date'],
                        'nb_pers' => $_POST['nb_pers'],
                        'movie_id' => $idMovie,
                        'time' => $_POST['time']
                    ];
                    $movieReservationManager->insert($movieReservation);
                    header('Location:/Movie/indexForUser');
                }
            }
            return $this->twig->render('Home/reservation_form.html.twig', [
                'errors' => $errors,
            ]);
        } else {
            header('Location: /');
        }
    }
}
