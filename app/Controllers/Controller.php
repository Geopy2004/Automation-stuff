<?php

namespace App\Controllers;

use App\Models\ActivityLog;

abstract class Controller
{
    protected function view(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        $viewFile = BASE_PATH . '/app/Views/' . $view . '.php';
        require BASE_PATH . '/app/Views/layouts/app.php';
    }

    protected function csrf(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    protected function verifyCsrf(): void
    {
        $token = $_POST['_csrf'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            http_response_code(419);
            exit('Invalid CSRF token.');
        }
    }

    protected function requireAuth(): void
    {
        if (empty($_SESSION['user'])) {
            redirect('login');
        }
        if (($_SESSION['fingerprint'] ?? '') !== $this->fingerprint()) {
            session_destroy();
            redirect('login');
        }
    }

    protected function requireRole(string $role): void
    {
        $this->requireAuth();
        if (($_SESSION['user']['role'] ?? 'user') !== $role) {
            http_response_code(403);
            exit('Forbidden');
        }
    }

    protected function loginUser(array $user): void
    {
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => (int) $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
        ];
        $_SESSION['fingerprint'] = $this->fingerprint();
    }

    protected function currentUser(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    protected function flash(string $key, string $message): void
    {
        $_SESSION['flash'][$key] = $message;
    }

    protected function log(string $action, string $details = ''): void
    {
        (new ActivityLog())->create($this->currentUser()['id'] ?? null, $action, $details);
    }

    private function fingerprint(): string
    {
        return hash('sha256', ($_SERVER['HTTP_USER_AGENT'] ?? '') . '|automation-system');
    }
}
