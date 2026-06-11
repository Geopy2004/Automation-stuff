<?php

namespace App\Controllers;

use App\Models\User;

class UserController extends Controller
{
    public function index(): void
    {
        $this->requireRole('admin');
        $this->view('users/index', ['title' => 'User Management', 'users' => (new User())->all(), 'csrf' => $this->csrf()]);
    }

    public function create(): void
    {
        $this->requireRole('admin');
        $this->verifyCsrf();
        $role = ($_POST['role'] ?? 'user') === 'admin' ? 'admin' : 'user';
        (new User())->create(trim($_POST['name'] ?? ''), trim($_POST['email'] ?? ''), $_POST['password'] ?? '', $role);
        $this->log('user_create', $_POST['email'] ?? '');
        redirect('users');
    }

    public function toggle(): void
    {
        $this->requireRole('admin');
        $this->verifyCsrf();
        (new User())->toggle((int) ($_POST['id'] ?? 0));
        $this->log('user_toggle', 'User ID ' . ($_POST['id'] ?? ''));
        redirect('users');
    }
}
