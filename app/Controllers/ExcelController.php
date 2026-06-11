<?php

namespace App\Controllers;

use App\Models\ActivityLog;
use App\Models\GeneratedFile;

class ExcelController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();
        $this->view('excel/index', [
            'title' => 'Excel Automation',
            'files' => (new GeneratedFile())->latest('xlsx', 20),
            'csrf' => $this->csrf(),
        ]);
    }

    public function export(): void
    {
        $this->requireAuth();
        $this->verifyCsrf();

        $rows = [['Date', 'User', 'Action', 'Details']];
        foreach ((new ActivityLog())->latest(100) as $log) {
            $rows[] = [$log['created_at'], $log['name'] ?? 'System', $log['action'], $log['details'] ?? ''];
        }

        $name = 'activity-export-' . date('Ymd-His') . '.xlsx';
        $relative = 'storage/exports/' . $name;
        $path = BASE_PATH . '/' . $relative;
        $this->writeXlsx($path, $rows);

        (new GeneratedFile())->create($this->currentUser()['id'], 'xlsx', 'Activity Export', $relative);
        $this->log('xlsx_export', $name);
        $this->flash('success', 'Excel export generated.');
        redirect('excel');
    }

    private function writeXlsx(string $path, array $rows): void
    {
        $zip = new \ZipArchive();
        $zip->open($path, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/></Types>');
        $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>');
        $zip->addFromString('xl/workbook.xml', '<?xml version="1.0" encoding="UTF-8"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets><sheet name="Activity Logs" sheetId="1" r:id="rId1"/></sheets></workbook>');
        $zip->addFromString('xl/_rels/workbook.xml.rels', '<?xml version="1.0" encoding="UTF-8"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/></Relationships>');

        $sheet = '<?xml version="1.0" encoding="UTF-8"?><worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>';
        foreach ($rows as $r => $row) {
            $sheet .= '<row r="' . ($r + 1) . '">';
            foreach ($row as $c => $value) {
                $cell = chr(65 + $c) . ($r + 1);
                $sheet .= '<c r="' . $cell . '" t="inlineStr"><is><t>' . e($value) . '</t></is></c>';
            }
            $sheet .= '</row>';
        }
        $sheet .= '</sheetData></worksheet>';
        $zip->addFromString('xl/worksheets/sheet1.xml', $sheet);
        $zip->close();
    }
}
