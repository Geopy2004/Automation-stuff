<?php

namespace App\Controllers;

use App\Models\GeneratedFile;

class WordController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();
        $this->view('word/index', [
            'title' => 'Word Automation',
            'files' => (new GeneratedFile())->latest('docx', 20),
            'csrf' => $this->csrf(),
        ]);
    }

    public function generate(): void
    {
        $this->requireAuth();
        $this->verifyCsrf();
        $title = trim($_POST['title'] ?? 'Automation Report');
        $content = trim($_POST['content'] ?? '');
        if ($content === '') {
            $this->flash('error', 'Document content is required.');
            redirect('word');
        }

        $name = 'document-' . date('Ymd-His') . '.docx';
        $relative = 'storage/documents/' . $name;
        $this->writeDocx(BASE_PATH . '/' . $relative, $title, $content);
        (new GeneratedFile())->create($this->currentUser()['id'], 'docx', $title, $relative);
        $this->log('docx_generate', $name);
        $this->flash('success', 'Word document generated.');
        redirect('word');
    }

    private function writeDocx(string $path, string $title, string $content): void
    {
        $paragraphs = '';
        foreach (preg_split('/\R+/', $content) as $line) {
            $paragraphs .= '<w:p><w:r><w:t>' . e($line) . '</w:t></w:r></w:p>';
        }
        $document = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"><w:body><w:p><w:r><w:rPr><w:b/></w:rPr><w:t>' . e($title) . '</w:t></w:r></w:p>' . $paragraphs . '</w:body></w:document>';
        $zip = new \ZipArchive();
        $zip->open($path, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/></Types>');
        $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/></Relationships>');
        $zip->addFromString('word/document.xml', $document);
        $zip->close();
    }
}
