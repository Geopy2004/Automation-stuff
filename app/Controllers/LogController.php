<?php

namespace App\Controllers;

use App\Models\ActivityLog;

class LogController extends Controller
{
    public function index(): void
    {
        $this->requireRole('admin');
        $this->view('logs/index', ['title' => 'Activity Logs', 'logs' => (new ActivityLog())->latest(100), 'csrf' => $this->csrf()]);
    }
}
