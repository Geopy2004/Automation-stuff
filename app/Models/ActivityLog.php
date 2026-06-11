<?php

namespace App\Models;

class ActivityLog extends Model
{
    public function create(?int $userId, string $action, string $details = ''): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO activity_logs (user_id, action, details, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $userId,
            $action,
            $details,
            $_SERVER['REMOTE_ADDR'] ?? null,
            substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
        ]);
    }

    public function latest(int $limit = 15): array
    {
        $stmt = $this->db->prepare(
            'SELECT l.*, u.name FROM activity_logs l LEFT JOIN users u ON u.id = l.user_id ORDER BY l.created_at DESC LIMIT ?'
        );
        $stmt->bindValue(1, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function count(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM activity_logs')->fetchColumn();
    }
}
