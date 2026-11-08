<?php

declare(strict_types=1);

return static function (\PDO $db): void {
    $db->exec(
        "CREATE TABLE IF NOT EXISTS followups_status_history (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            followup_id INT UNSIGNED NOT NULL,
            changed_user_id INT UNSIGNED NOT NULL,
            old_status ENUM('open', 'completed'),
            new_status ENUM('open', 'completed'),
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    
            FOREIGN KEY (followup_id) REFERENCES followups(id) ON DELETE CASCADE,
            FOREIGN KEY (changed_user_id) REFERENCES users(id) ON DELETE RESTRICT,
    
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );
};

