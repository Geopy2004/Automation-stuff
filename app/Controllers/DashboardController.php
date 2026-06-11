<?php

namespace App\Controllers;

use App\Models\ActivityLog;
use App\Models\EmailMessage;
use App\Models\GeneratedFile;
use App\Models\User;

class DashboardController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();
        $files = new GeneratedFile();
        $this->view('dashboard/index', [
            'title' => 'Dashboard',
            'user' => $this->currentUser(),
            'stats' => [
                'users' => (new User())->count(),
                'emails' => (new EmailMessage())->count(),
                'exports' => $files->count('xlsx'),
                'documents' => $files->count('docx'),
                'logs' => (new ActivityLog())->count(),
            ],
            'logs' => (new ActivityLog())->latest(8),
            'files' => $files->latest(null, 8),
            'csrf' => $this->csrf(),
        ]);
    }
}
