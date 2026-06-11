<?php

declare(strict_types=1);

require dirname(__DIR__) . '/app/bootstrap.php';

use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\EmailController;
use App\Controllers\ExcelController;
use App\Controllers\LogController;
use App\Controllers\UserController;
use App\Controllers\WordController;

$path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$base = trim(parse_url(config('app.url'), PHP_URL_PATH), '/');
if ($base !== '' && str_starts_with($path, $base)) {
    $path = trim(substr($path, strlen($base)), '/');
}
$path = $path === '' ? 'login' : $path;
$method = $_SERVER['REQUEST_METHOD'];

$routes = [
    'GET login' => [AuthController::class, 'login'],
    'POST login' => [AuthController::class, 'authenticate'],
    'GET register' => [AuthController::class, 'register'],
    'POST register' => [AuthController::class, 'store'],
    'POST logout' => [AuthController::class, 'logout'],
    'GET dashboard' => [DashboardController::class, 'index'],
    'GET email' => [EmailController::class, 'index'],
    'POST email/sync' => [EmailController::class, 'sync'],
    'GET excel' => [ExcelController::class, 'index'],
    'POST excel/export' => [ExcelController::class, 'export'],
    'GET word' => [WordController::class, 'index'],
    'POST word/generate' => [WordController::class, 'generate'],
    'GET logs' => [LogController::class, 'index'],
    'GET users' => [UserController::class, 'index'],
    'POST users/create' => [UserController::class, 'create'],
    'POST users/toggle' => [UserController::class, 'toggle'],
];

$key = $method . ' ' . $path;
if (!isset($routes[$key])) {
    http_response_code(404);
    exit('Page not found');
}

[$class, $action] = $routes[$key];
(new $class())->$action();
