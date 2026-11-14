<?php

declare(strict_types=1);

return new class
{
    public function up(\PDO $db): void
    {
        $db->exec(
            "CREATE TABLE IF NOT EXISTS tests (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            title VARCHAR(150) NOT NULL,
            description TEXT NOT NULL,
            priority ENUM('low', 'normal', 'high') NOT NULL DEFAULT 'normal',
            status ENUM('open', 'completed') NOT NULL DEFAULT 'open',
            due_at DATETIME NOT NULL,
            completed_at DATETIME NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );
    }

    public function down(\PDO $db): void
    {
        $db->exec("DROP TABLE IF EXISTS tests");
    }
};