<?php

namespace App\Controllers;

use App\Models\EmailMessage;

class EmailController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();
        $this->view('email/index', [
            'title' => 'Email Automation',
            'messages' => (new EmailMessage())->latest(20),
            'csrf' => $this->csrf(),
        ]);
    }

    public function sync(): void
    {
        $this->requireAuth();
        $this->verifyCsrf();

        if (!extension_loaded('imap')) {
            $this->flash('error', 'PHP IMAP extension is not enabled.');
            redirect('email');
        }

        $mail = config('mail');
        if (!$mail['username'] || !$mail['password']) {
            $this->flash('error', 'Add Gmail IMAP credentials to .env first.');
            redirect('email');
        }

        $mailbox = sprintf('{%s:%s/imap/%s}INBOX', $mail['imap_host'], $mail['imap_port'], $mail['imap_encryption']);
        $imap = @imap_open($mailbox, $mail['username'], $mail['password']);
        if (!$imap) {
            $this->flash('error', 'Could not connect to Gmail IMAP.');
            redirect('email');
        }

        $ids = imap_search($imap, 'ALL') ?: [];
        $ids = array_slice(array_reverse($ids), 0, 10);
        $model = new EmailMessage();
        foreach ($ids as $id) {
            $header = imap_headerinfo($imap, $id);
            $body = imap_fetchbody($imap, $id, '1') ?: '';
            $sender = $header->fromaddress ?? 'Unknown';
            $subject = imap_utf8($header->subject ?? '(No subject)');
            $receivedAt = isset($header->date) ? date('Y-m-d H:i:s', strtotime($header->date)) : null;
            $model->create($this->currentUser()['id'], $subject, $sender, $receivedAt, substr(strip_tags($body), 0, 500));
        }
        imap_close($imap);

        $this->log('email_sync', 'Synced ' . count($ids) . ' messages.');
        $this->flash('success', 'Email sync completed.');
        redirect('email');
    }
}
