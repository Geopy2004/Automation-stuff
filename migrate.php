<?php

require __DIR__ . '/app/bootstrap.php';

use App\Models\Database;

$pdo = Database::connection();

$sql = [
    "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(120) NOT NULL,
        email VARCHAR(160) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('admin','user') NOT NULL DEFAULT 'user',
        is_active TINYINT(1) NOT NULL DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
    "CREATE TABLE IF NOT EXISTS activity_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NULL,
        action VARCHAR(120) NOT NULL,
        details TEXT NULL,
        ip_address VARCHAR(64) NULL,
        user_agent VARCHAR(255) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX(user_id),
        CONSTRAINT fk_activity_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
    "CREATE TABLE IF NOT EXISTS email_messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        subject VARCHAR(255) NOT NULL,
        sender VARCHAR(255) NOT NULL,
        received_at DATETIME NULL,
        snippet TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX(user_id),
        CONSTRAINT fk_email_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
    "CREATE TABLE IF NOT EXISTS generated_files (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        type ENUM('xlsx','docx') NOT NULL,
        title VARCHAR(180) NOT NULL,
        file_path VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX(user_id),
        CONSTRAINT fk_file_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
];

foreach ($sql as $statement) {
    $pdo->exec($statement);
}

function columnExists(PDO $pdo, string $table, string $column): bool
{
    $stmt = $pdo->prepare(
        'SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?'
    );
    $stmt->execute([$table, $column]);
    return (int) $stmt->fetchColumn() > 0;
}

function indexExists(PDO $pdo, string $table, string $index): bool
{
    $stmt = $pdo->prepare(
        'SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND INDEX_NAME = ?'
    );
    $stmt->execute([$table, $index]);
    return (int) $stmt->fetchColumn() > 0;
}

if (!columnExists($pdo, 'users', 'name')) {
    $pdo->exec("ALTER TABLE users ADD name VARCHAR(120) NULL AFTER id");
    if (columnExists($pdo, 'users', 'fullname')) {
        $pdo->exec("UPDATE users SET name = fullname WHERE name IS NULL OR name = ''");
    }
    $pdo->exec("UPDATE users SET name = COALESCE(NULLIF(name, ''), username, 'User')");
    $pdo->exec("ALTER TABLE users MODIFY name VARCHAR(120) NOT NULL");
}

if (!columnExists($pdo, 'users', 'email')) {
    $pdo->exec("ALTER TABLE users ADD email VARCHAR(160) NULL AFTER name");
    if (columnExists($pdo, 'users', 'username')) {
        $pdo->exec("UPDATE users SET email = CASE WHEN username LIKE '%@%' THEN username ELSE CONCAT(username, '@local.test') END WHERE email IS NULL OR email = ''");
    }
    $pdo->exec("UPDATE users SET email = CONCAT('user', id, '@local.test') WHERE email IS NULL OR email = ''");
    $pdo->exec("ALTER TABLE users MODIFY email VARCHAR(160) NOT NULL");
}

if (!indexExists($pdo, 'users', 'email')) {
    $pdo->exec("ALTER TABLE users ADD UNIQUE KEY email (email)");
}

if (!columnExists($pdo, 'users', 'role')) {
    $pdo->exec("ALTER TABLE users ADD role ENUM('admin','user') NOT NULL DEFAULT 'user' AFTER password");
}

if (!columnExists($pdo, 'users', 'is_active')) {
    $pdo->exec("ALTER TABLE users ADD is_active TINYINT(1) NOT NULL DEFAULT 1 AFTER role");
}

$count = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
if ($count === 0) {
    $stmt = $pdo->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)');
    $stmt->execute(['System Admin', 'admin@example.com', password_hash('ChangeMe123!', PASSWORD_DEFAULT), 'admin']);
    echo "Created default admin: admin@example.com / ChangeMe123!\n";
}

echo "Migration complete.\n";
