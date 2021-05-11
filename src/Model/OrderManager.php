<?php

namespace App\Model;

use PDO;

class OrderManager extends AbstractManager
{
    public const TABLE = 'order';

    /**
     *  Initializes this class.
     */
    public function __construct()
    {
        parent::__construct(self::TABLE);
    }

    public function selectAll(): array
    {
        return $this->pdo->query("SELECT * FROM `order` ORDER BY id DESC")->fetchAll();
    }

    public function insert(array $order)
    {
        $statement = $this->pdo->prepare(
            "INSERT INTO `order` (total, user_id, order_date) 
        VALUES (:total, :user_id, :order_date)"
        );
        $statement->bindValue('total', $order['total'], PDO::PARAM_INT);
        $statement->bindValue('user_id', $order['user_id'], PDO::PARAM_INT);
        $statement->bindValue('order_date', $order['order_date'], PDO::PARAM_STR);

        $statement->execute();

        return (int)$this->pdo->lastInsertId();
    }

    public function getOrdersByUser(int $idUser)
    {
        $statement = $this->pdo->prepare("SELECT * FROM `order` WHERE user_id= :user_id");
        $statement->bindValue('user_id', $idUser, PDO::PARAM_INT);

        $statement->execute();

        return $statement->fetchAll();
    }

    public function getDateFromOrder(int $idOrder)
    {
        $statement = $this->pdo->prepare("SELECT order_date FROM `order`");
        $statement->execute();

        return $statement->fetchAll();
    }

    public function getAllOrders()
    {
        $statement = $this->pdo->prepare('SELECT count(*) From `order`');
        $statement->execute();
        return $statement->fetchColumn();
    }

    public function getTotals()
    {
        $statement = $this->pdo->prepare('SELECT sum(total) From `order`');
        $statement->execute();
        return $statement->fetchColumn();
    }

    public function selectAllForOrders(): array
    {
        return $this->pdo->query(
            "SELECT order.id, order.order_date, order.total, order.user_id, user.username
        FROM `order` JOIN `user` ON user.id = order.user_id"
        )->fetchAll();
    }
}
