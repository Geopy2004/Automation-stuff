<?php

namespace App\Controllers;

use App\Models\User;

class AuthController extends Controller
{
    public function login(): void
    {
        $this->view('auth/login', ['title' => 'Login', 'csrf' => $this->csrf()]);
    }

    public function authenticate(): void
    {
        $this->verifyCsrf();
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'] ?? '';
        $user = $email ? (new User())->findByEmail($email) : null;

        if (!$user || !$user['is_active'] || !password_verify($password, $user['password'])) {
            $this->flash('error', 'Invalid credentials or inactive account.');
            redirect('login');
        }

        $this->loginUser($user);
        $this->log('login', 'User signed in.');
        redirect('dashboard');
    }

    public function register(): void
    {
        $this->view('auth/register', ['title' => 'Register', 'csrf' => $this->csrf()]);
    }

    public function store(): void
    {
        $this->verifyCsrf();
        $name = trim($_POST['name'] ?? '');
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'] ?? '';

        if ($name === '' || !$email || strlen($password) < 8) {
            $this->flash('error', 'Use a valid name, email, and password with at least 8 characters.');
            redirect('register');
        }

        try {
            (new User())->create($name, $email, $password, 'user');
            $this->flash('success', 'Account created. You can sign in now.');
            redirect('login');
        } catch (\Throwable) {
            $this->flash('error', 'That email is already registered.');
            redirect('register');
        }
    }

    public function logout(): void
    {
        $this->verifyCsrf();
        $this->log('logout', 'User signed out.');
        session_destroy();
        redirect('login');
    }
}
