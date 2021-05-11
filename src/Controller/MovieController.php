<?php

namespace App\Controller;

use App\Model\MovieManager;

/**
 * Class movieController
 *
 */
class MovieController extends AbstractController
{
    /**
     * Display movie listing
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */

    public function indexForUser()
    {
        $movieManager = new MovieManager();
        $movies = $movieManager->selectAll();

        return $this->twig->render('Home/movies.html.twig', ['movies' => $movies]);
    }

    public function showForUser(int $id)
    {
        $movieManager = new MovieManager();
        $movie = $movieManager->selectOneById($id);

        return $this->twig->render('Home/movie.html.twig', ['movie' => $movie]);
    }
    public function filterExPrice()
    {
        $movieManager = new MovieManager();
        $movies = $movieManager->selectAllbyExPrice();

        return $this->twig->render('Home/movies.html.twig', ['movies' => $movies]);
    }
    public function filterChPrice()
    {
        $movieManager = new MovieManager();
        $movies = $movieManager->selectAllbyChPrice();

        return $this->twig->render('Home/movies.html.twig', ['movies' => $movies]);
    }
    public function filterLong()
    {
        $movieManager = new MovieManager();
        $movies = $movieManager->selectAllbyLong();

        return $this->twig->render('Home/movies.html.twig', ['movies' => $movies]);
    }
    public function filterShort()
    {
        $movieManager = new MovieManager();
        $movies = $movieManager->selectAllbyShort();

        return $this->twig->render('Home/movies.html.twig', ['movies' => $movies]);
    }
}
