<?php

declare(strict_types=1);

return new class {
    public function up(\PDO $db): void
    {
        $db->exec(
            "CREATE TABLE IF NOT EXISTS followups (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            customer_id INT UNSIGNED NOT NULL,
            creator_id INT UNSIGNED NOT NULL,
            title VARCHAR(150) NOT NULL,
            description TEXT NOT NULL,
            priority ENUM('low', 'normal', 'high') NOT NULL DEFAULT 'normal',
            status ENUM('open', 'completed') NOT NULL DEFAULT 'open',
            due_at DATETIME NOT NULL,
            completed_at DATETIME NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    
    
            FOREIGN KEY (customer_id)
            REFERENCES customers(id)
            ON DELETE RESTRICT,
    
            FOREIGN KEY (creator_id)
            REFERENCES users(id)
            ON DELETE RESTRICT,
    
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );
    }

    public function down(\PDO $db): void
    {
        $db->exec("DROP TABLE IF EXISTS followups");
    }
};

