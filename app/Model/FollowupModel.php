<?php
declare(strict_types=1);

namespace Assessment\Model;

use PDO;

final class FollowupModel
{
    public PDO $db;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    public function create(array $data)
    {
        $query = "INSERT INTO followups (customer_id, creator_id,title,description, priority, due_at)
            VALUES (:customer_id, :creator_id, :title, :description, :priority, :due_at)
        ";

        $statement = $this->db->prepare($query);
        $statement->execute($data);

        return $this->db->lastInsertId();
    }
}