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

    public function index(int $page, int $perPage)
    {
        $offset = ($page - 1) * $perPage;
        $query = "SELECT * FROM followups ORDER BY id DESC LIMIT :limit OFFSET :offsetPage";

        $statement = $this->db->prepare($query);
        $statement->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $statement->bindValue(':offsetPage', $offset, PDO::PARAM_INT);

        $statement->execute();

        return $statement->fetchAll();
    }

    public function getAll()
    {
        $query = "SELECT * FROM followups";

        $statement = $this->db->prepare($query);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function show(int $id)
    {
        $query = "SELECT * FROM followups WHERE id = :id";

        $statement = $this->db->prepare($query);
        $statement->execute([$id]);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}