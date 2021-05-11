<?php

namespace App\Model;

class MovieManager extends AbstractManager
{
    public const TABLE = 'movie';

    public function __construct()
    {
        parent::__construct(self::TABLE);
    }

    public function insert(array $movie): int
    {
        // prepared request
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE . "
        (`name`, `minutes`, `description`, `price`, `image`) 
        VALUES (:name, :minutes, :description, :price, :image)");
        $statement->bindValue('name', $movie['name'], \PDO::PARAM_STR);
        $statement->bindValue('minutes', $movie['minutes'], \PDO::PARAM_INT);
        $statement->bindValue('description', $movie['description'], \PDO::PARAM_STR);
        $statement->bindValue('price', $movie['price'], \PDO::PARAM_STR);
        $statement->bindValue('image', $movie['image'], \PDO::PARAM_STR);

        $statement->execute();
        return (int)$this->pdo->lastInsertId();
    }

    public function delete(int $id): void
    {
        // prepared request
        $statement = $this->pdo->prepare("DELETE FROM " . self::TABLE . " WHERE id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();
    }

    public function update(array $movie): bool
    {
        // prepared request
        $statement = $this->pdo->prepare("UPDATE " . self::TABLE .
            " SET `name` = :name, `minutes` = :minutes, `description` = :description,  `price` = :price, `image` = :image
        WHERE id=:id");
        $statement->bindValue('id', $movie['id'], \PDO::PARAM_INT);
        $statement->bindValue('name', $movie['name'], \PDO::PARAM_STR);
        $statement->bindValue('minutes', $movie['minutes'], \PDO::PARAM_INT);
        $statement->bindValue('description', $movie['description'], \PDO::PARAM_STR);
        $statement->bindValue('price', $movie['price'], \PDO::PARAM_STR);
        $statement->bindValue('image', $movie['image'], \PDO::PARAM_STR);
        return $statement->execute();
    }
    public function selectAllbyExPrice(): array
    {
        return $this->pdo->query("SELECT id,
        name, minutes, price, image
        FROM " . self::TABLE . " ORDER BY price DESC")->fetchAll();
    }

    public function selectAllbyChPrice(): array
    {
        return $this->pdo->query("SELECT id,
        name, minutes, price, image
        FROM " . self::TABLE . " ORDER BY price ASC")->fetchAll();
    }
    public function selectAllbyLong(): array
    {
        return $this->pdo->query("SELECT id,
        name, minutes, price, image
        FROM " . self::TABLE . " ORDER BY minutes DESC")->fetchAll();
    }
    public function selectAllbyShort(): array
    {
        return $this->pdo->query("SELECT id,
        name, minutes, price, image
        FROM " . self::TABLE . " ORDER BY minutes ASC")->fetchAll();
    }
}
