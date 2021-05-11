<?php

/**
 * Created by PhpStorm.
 * User: sylvain
 * Date: 07/03/18
 * Time: 18:20
 * PHP version 7
 */

namespace App\Model;

/**
 *
 */
class MovieReservationManager extends AbstractManager
{
    public const TABLE = 'movie_reservation';

    /**
     *  Initializes this class.
     */
    public function __construct()
    {
        parent::__construct(self::TABLE);
    }

    /**
     * @param array $movieReservation
     * @return int
     */
    public function insert(array $movieReservation): int
    {
        // prepared request
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE . " (`date`, `nb_pers`, `movie_id`, `time`) 
        VALUES (:date, :nb_pers, :movie_id, :time)");
        $statement->bindValue('date', $movieReservation['date'], \PDO::PARAM_STR);
        $statement->bindValue('nb_pers', $movieReservation['nb_pers'], \PDO::PARAM_INT);
        $statement->bindValue('movie_id', $movieReservation['movie_id'], \PDO::PARAM_INT);
        $statement->bindValue('time', $movieReservation['time'], \PDO::PARAM_INT);

        $statement->execute();
        return (int)$this->pdo->lastInsertId();
    }
}
