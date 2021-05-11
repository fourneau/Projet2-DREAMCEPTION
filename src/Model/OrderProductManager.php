<?php

namespace App\Model;

use PDO;

class OrderProductManager extends AbstractManager
{
    public const TABLE = 'order_product';

    /**
     *  Initializes this class.
     */
    public function __construct()
    {
        parent::__construct(self::TABLE);
    }

    public function selectAll(): array
    {
        return $this->pdo->query('SELECT * FROM' . self::TABLE . 'ORDER BY id DESC')->fetchAll();
    }

    public function insert(array $order)
    {
        $statement = $this->pdo->prepare("INSERT INTO `order_product` (order_id, product_id, qty, address) 
        VALUES (:order_id, :product_id, :qty, :address)");
        $statement->bindValue('order_id', $order['order_id'], PDO::PARAM_INT);
        $statement->bindValue('product_id', $order['product_id'], PDO::PARAM_INT);
        $statement->bindValue('qty', $order['qty'], PDO::PARAM_INT);
        $statement->bindValue('address', $order['address'], PDO::PARAM_STR);

        $statement->execute();

        return (int) $this->pdo->lastInsertId();
    }

    public function getTicketFromOrderId(int $orderId): array
    {
        $statement = $this->pdo->prepare("SELECT * FROM `order_product` WHERE order_id= :order_id");
        $statement->bindValue('order_id', $orderId, PDO::PARAM_INT);

        $statement->execute();

        return $statement->fetchAll();
    }
}
