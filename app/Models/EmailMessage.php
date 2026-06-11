<?php

namespace App\Models;

class EmailMessage extends Model
{
    public function create(int $userId, string $subject, string $sender, ?string $receivedAt, string $snippet): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO email_messages (user_id, subject, sender, received_at, snippet) VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([$userId, $subject, $sender, $receivedAt, $snippet]);
    }

    public function latest(int $limit = 10): array
    {
        $stmt = $this->db->prepare('SELECT * FROM email_messages ORDER BY received_at DESC, created_at DESC LIMIT ?');
        $stmt->bindValue(1, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function count(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM email_messages')->fetchColumn();
    }
}
