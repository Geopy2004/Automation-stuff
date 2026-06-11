<?php

namespace App\Models;

class GeneratedFile extends Model
{
    public function create(int $userId, string $type, string $title, string $path): void
    {
        $stmt = $this->db->prepare('INSERT INTO generated_files (user_id, type, title, file_path) VALUES (?, ?, ?, ?)');
        $stmt->execute([$userId, $type, $title, $path]);
    }

    public function latest(?string $type = null, int $limit = 10): array
    {
        if ($type) {
            $stmt = $this->db->prepare('SELECT * FROM generated_files WHERE type = ? ORDER BY created_at DESC LIMIT ?');
            $stmt->bindValue(1, $type);
            $stmt->bindValue(2, $limit, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        }
        $stmt = $this->db->prepare('SELECT * FROM generated_files ORDER BY created_at DESC LIMIT ?');
        $stmt->bindValue(1, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function count(?string $type = null): int
    {
        if (!$type) {
            return (int) $this->db->query('SELECT COUNT(*) FROM generated_files')->fetchColumn();
        }
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM generated_files WHERE type = ?');
        $stmt->execute([$type]);
        return (int) $stmt->fetchColumn();
    }
}
