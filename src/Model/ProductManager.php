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
class ProductManager extends AbstractManager
{
    public const TABLE = 'product';

    /**
     *  Initializes this class.
     */
    public function __construct()
    {
        parent::__construct(self::TABLE);
    }

    public function selectAllForCategory(): array
    {
        return $this->pdo->query("SELECT product.id,
        product.name, price, stock, description, image, weight, category_id, category.name as category_name
        FROM " . self::TABLE . "
        JOIN category ON category.id = product.category_id")->fetchAll();
    }

    public function selectOneByIdForCategory(int $id)
    {
        // prepared request
        $statement = $this->pdo->prepare("SELECT product.id,
        product.name, price, stock, description, image, weight, category_id, category.name as category_name
        FROM " . self::TABLE . " 
        JOIN category ON category.id = product.category_id
        WHERE product.id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetch();
    }

    /**
     * @param array $product
     * @return int
     */
    public function insert(array $product): int
    {
        // prepared request
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE . " 
        (`name`, `price`, `stock`, `description`, `image`, `weight`, `category_id`) 
        VALUES (:name, :price, :stock, :description, :image, :weight, :category_id)");
        $statement->bindValue('name', $product['name'], \PDO::PARAM_STR);
        $statement->bindValue('price', $product['price'], \PDO::PARAM_STR);
        $statement->bindValue('stock', $product['stock'], \PDO::PARAM_INT);
        $statement->bindValue('description', $product['description'], \PDO::PARAM_STR);
        $statement->bindValue('image', $product['image'], \PDO::PARAM_STR);
        $statement->bindValue('weight', $product['weight'], \PDO::PARAM_STR);
        $statement->bindValue('category_id', $product['category_id'], \PDO::PARAM_INT);

        $statement->execute();
        return (int)$this->pdo->lastInsertId();
    }


    /**
     * @param int $id
     */
    public function delete(int $id): void
    {
        // prepared request
        $statement = $this->pdo->prepare("DELETE FROM " . self::TABLE . " WHERE id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();
    }


    /**
     * @param array $product
     * @return bool
     */
    public function update(array $product): bool
    {
        // prepared request
        $statement = $this->pdo->prepare("UPDATE " . self::TABLE .
            " SET `name` = :name, `price` = :price, `stock` = 
            :stock, `description` = :description, `image` = :image, `weight` = :weight, `category_id` = :category_id 
        WHERE id=:id");
        $statement->bindValue('id', $product['id'], \PDO::PARAM_INT);
        $statement->bindValue('name', $product['name'], \PDO::PARAM_STR);
        $statement->bindValue('price', $product['price'], \PDO::PARAM_STR);
        $statement->bindValue('stock', $product['stock'], \PDO::PARAM_INT);
        $statement->bindValue('description', $product['description'], \PDO::PARAM_STR);
        $statement->bindValue('image', $product['image'], \PDO::PARAM_STR);
        $statement->bindValue('weight', $product['weight'], \PDO::PARAM_STR);
        $statement->bindValue('category_id', $product['category_id'], \PDO::PARAM_INT);

        return $statement->execute();
    }

    public function selectAll(): array
    {
        return $this->pdo->query("SELECT product.id,
        product.name, price, stock, description, image, weight, category_id, category.name as category_name
        FROM " . self::TABLE . "
        JOIN category ON category.id = product.category_id WHERE product.archived is NULL")->fetchAll();
    }

    public function selectOneById(int $id)
    {
        // prepared request
        $statement = $this->pdo->prepare("SELECT product.id,
        product.name, price, stock, description, image, weight, category_id, category.name as category_name
        FROM " . self::TABLE . " 
        JOIN category ON category.id = product.category_id
        WHERE product.id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetch();
    }

    public function selectAllbyCat(): array
    {
        return $this->pdo->query("SELECT product.name, product.price, product.stock, product.description, product.image, category_id, product.id, category.name as category_name
        FROM " . self::TABLE . "
        JOIN category ON category.id = product.category_id WHERE 
        category.id = product.category_id AND product.archived is NULL")->fetchAll();
    }

    public function selectAllbyExPrice(): array
    {
        return $this->pdo->query("SELECT id,
        name, price, stock, description, image
        FROM " . self::TABLE . " WHERE product.archived is NULL ORDER BY price DESC")->fetchAll();
    }

    public function selectAllbyChPrice(): array
    {
        return $this->pdo->query("SELECT id,
        name, price, stock, description, image
        FROM " . self::TABLE . " WHERE product.archived is NULL ORDER BY price ASC")->fetchAll();
    }


    //Gestion du stock

    public function updateQty(int $idProduct, int $newqty): bool
    {
        // prepared request
        $statement = $this->pdo->prepare("UPDATE " . self::TABLE . " SET `stock` = :stock WHERE id=:id");
        $statement->bindValue('id', $idProduct, \PDO::PARAM_INT);
        $statement->bindValue('stock', $newqty, \PDO::PARAM_INT);
        return $statement->execute();
    }

    public function softdelete(int $idProduct, int $archived): bool
    {
        // prepared request
        $statement = $this->pdo->prepare("UPDATE " . self::TABLE . " SET `archived` = :archived WHERE id=:id");
        $statement->bindValue('id', $idProduct, \PDO::PARAM_INT);
        $statement->bindValue('archived', $archived, \PDO::PARAM_INT);
        return $statement->execute();
    }
}
