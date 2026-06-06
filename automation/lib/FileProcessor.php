<?php
// File processor library - handles Excel and Word file processing

class FileProcessor {
    private $conn;
    private $uploadDir;
    
    public function __construct($conn, $uploadDir) {
        $this->conn = $conn;
        $this->uploadDir = $uploadDir;
    }

    public function processUploadedFile($file, $processingType, $targetTable, $notes) {
        $filename = $file['name'];
        $fileTmpName = $file['tmp_name'];
        $fileSize = $file['size'];
        
        // Validate file
        $validation = $this->validateFile($filename, $fileSize);
        if (!$validation['valid']) {
            return ['success' => false, 'message' => $validation['message']];
        }

        $fileType = $validation['type'];
        $fileExt = $validation['ext'];
        
        // Save file
        $newFilename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
        $filePath = $this->uploadDir . '/' . $newFilename;
        
        if (!move_uploaded_file($fileTmpName, $filePath)) {
            return ['success' => false, 'message' => 'Failed to save file'];
        }

        // Register in database
        $uploadId = $this->registerUpload($newFilename, $filePath, $fileType, $fileSize, $processingType, $targetTable, $notes);
        
        // Process file based on type
        if ($fileType === 'excel') {
            $result = $this->processExcelFile($filePath, $uploadId, $fileExt);
        } else {
            $result = $this->processWordFile($filePath, $uploadId, $fileExt);
        }

        if ($result['success']) {
            // Update upload record
            $recordCount = $result['recordCount'];
            $this->conn->query("UPDATE file_uploads SET record_count = $recordCount, status = 'completed' WHERE id = $uploadId");
            
            return ['success' => true, 'message' => "File processed successfully! Extracted $recordCount records.", 'uploadId' => $uploadId];
        } else {
            $this->conn->query("UPDATE file_uploads SET status = 'error', error_message = '" . $this->conn->escape_string($result['message']) . "' WHERE id = $uploadId");
            return ['success' => false, 'message' => 'Error processing file: ' . $result['message']];
        }
    }

    private function validateFile($filename, $fileSize) {
        $maxSize = 50 * 1024 * 1024; // 50 MB
        $allowedExts = ['xlsx', 'xls', 'docx', 'doc'];
        
        if ($fileSize > $maxSize) {
            return ['valid' => false, 'message' => 'File size exceeds 50 MB limit'];
        }

        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExts)) {
            return ['valid' => false, 'message' => 'File type not supported. Use Excel (.xlsx, .xls) or Word (.docx, .doc)'];
        }

        $type = in_array($ext, ['xlsx', 'xls']) ? 'excel' : 'word';
        return ['valid' => true, 'type' => $type, 'ext' => $ext];
    }

    private function registerUpload($filename, $filePath, $fileType, $fileSize, $processingType, $targetTable, $notes) {
        $sql = "INSERT INTO file_uploads (filename, file_path, file_type, file_size, processing_type, target_table, notes, status) 
                VALUES ('$filename', '$filePath', '$fileType', $fileSize, '$processingType', '$targetTable', '" . $this->conn->escape_string($notes) . "', 'processing')";
        $this->conn->query($sql);
        return $this->conn->insert_id;
    }

    public function processExcelFile($filePath, $uploadId, $fileExt = null) {
        try {
            if ($fileExt === 'xls') {
                return ['success' => false, 'message' => 'Legacy .xls files are not supported. Please save the spreadsheet as .xlsx and upload again.'];
            }

            $rows = $this->extractExcelRows($filePath, $fileExt);
            if (empty($rows)) {
                return ['success' => false, 'message' => 'No readable rows found in Excel file'];
            }

            $recordCount = 0;
            $headers = null;

            foreach ($rows as $rowIndex => $row) {
                $rowNumber = $rowIndex + 1;
                
                // First row = headers
                if ($headers === null) {
                    $headers = $this->normalizeHeaders($row);
                    continue;
                }

                if ($this->isEmptyRow($row)) {
                    continue;
                }

                // Extract data
                $rawData = [];
                foreach ($headers as $i => $header) {
                    $rawData[$header] = isset($row[$i]) ? $row[$i] : '';
                }

                // Save extracted data
                $this->insertExtractedData($uploadId, $rowNumber, $rawData);
                $recordCount++;

                // Log progress
                if ($recordCount % 100 === 0) {
                    $this->logProcessing($uploadId, 'extract', "Processed $recordCount records", 'in_progress');
                }
            }

            $this->logProcessing($uploadId, 'extract', "Completed extraction of $recordCount records", 'completed');
            return ['success' => true, 'recordCount' => $recordCount];

        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function processWordFile($filePath, $uploadId, $fileExt = null) {
        try {
            if ($fileExt === 'doc') {
                return ['success' => false, 'message' => 'Legacy .doc files are not supported. Please save the document as .docx and upload again.'];
            }

            if (!class_exists('ZipArchive')) {
                return ['success' => false, 'message' => 'PHP Zip extension is required to read .docx files. Enable zip in php.ini and restart Apache.'];
            }

            // Extract text from Word document
            $content = $this->extractWordContent($filePath, true);
            
            // Parse content into structured data
            $tables = $this->parseWordTables($content);
            $recordCount = 0;
            $rowNumber = 0;

            foreach ($tables as $table) {
                foreach ($table as $row) {
                    $rowNumber++;
                    $this->insertExtractedData($uploadId, $rowNumber, $row);
                    $recordCount++;
                }
            }

            $this->logProcessing($uploadId, 'extract', "Extracted $recordCount records from Word document", 'completed');
            return ['success' => true, 'recordCount' => $recordCount];

        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    private function extractWordContent($filePath, $preserveRows = false) {
        // Simple Word document parsing (reads text content)
        $zip = new ZipArchive;
        if ($zip->open($filePath) === true) {
            if (($index = $zip->locateName('word/document.xml')) !== false) {
                $data = $zip->getFromIndex($index);
                $zip->close();
                
                $dom = new DOMDocument();
                libxml_use_internal_errors(true);
                $dom->loadXML($data);
                libxml_clear_errors();

                if (!$preserveRows) {
                    return $dom->textContent;
                }

                $xpath = new DOMXPath($dom);
                $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
                $rows = [];

                foreach ($xpath->query('//w:tbl//w:tr') as $tr) {
                    $cells = [];
                    foreach ($xpath->query('./w:tc', $tr) as $tc) {
                        $cells[] = trim($tc->textContent);
                    }
                    if (!empty(array_filter($cells, 'strlen'))) {
                        $rows[] = implode("\t", $cells);
                    }
                }

                if (!empty($rows)) {
                    return implode("\n", $rows);
                }

                $paragraphs = [];
                foreach ($xpath->query('//w:p') as $paragraph) {
                    $text = trim($paragraph->textContent);
                    if ($text !== '') {
                        $paragraphs[] = $text;
                    }
                }

                return implode("\n", $paragraphs);
            }
            $zip->close();
        }
        return '';
    }

    private function extractExcelRows($filePath, $fileExt) {
        if (!class_exists('ZipArchive')) {
            throw new Exception('PHP Zip extension is required to read .xlsx files. Enable zip in php.ini and restart Apache.');
        }

        $zip = new ZipArchive;
        if ($zip->open($filePath) !== true) {
            return $this->extractDelimitedRows($filePath);
        }

        $sharedStrings = $this->getSharedStrings($zip);
        $sheetName = $this->getFirstWorksheetName($zip);
        if ($sheetName === null) {
            $zip->close();
            return [];
        }

        $sheetXml = $zip->getFromName($sheetName);
        $zip->close();

        if ($sheetXml === false) {
            return [];
        }

        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadXML($sheetXml);
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('s', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
        $rows = [];

        foreach ($xpath->query('//s:sheetData/s:row') as $rowNode) {
            $row = [];
            foreach ($xpath->query('./s:c', $rowNode) as $cellNode) {
                $cellRef = $cellNode->getAttribute('r');
                $columnIndex = $this->excelColumnIndex($cellRef);
                while (count($row) < $columnIndex) {
                    $row[] = '';
                }
                $row[] = $this->readExcelCellValue($xpath, $cellNode, $sharedStrings);
            }
            $rows[] = $row;
        }

        return $rows;
    }

    private function getSharedStrings($zip) {
        $xml = $zip->getFromName('xl/sharedStrings.xml');
        if ($xml === false) {
            return [];
        }

        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadXML($xml);
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('s', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
        $strings = [];

        foreach ($xpath->query('//s:si') as $si) {
            $strings[] = $si->textContent;
        }

        return $strings;
    }

    private function getFirstWorksheetName($zip) {
        for ($i = 1; $i <= 50; $i++) {
            $name = "xl/worksheets/sheet$i.xml";
            if ($zip->locateName($name) !== false) {
                return $name;
            }
        }

        return null;
    }

    private function readExcelCellValue($xpath, $cellNode, $sharedStrings) {
        $type = $cellNode->getAttribute('t');

        if ($type === 'inlineStr') {
            return trim($cellNode->textContent);
        }

        $valueNode = $xpath->query('./s:v', $cellNode)->item(0);
        if (!$valueNode) {
            return '';
        }

        $value = $valueNode->nodeValue;
        if ($type === 's') {
            return $sharedStrings[(int) $value] ?? '';
        }

        if ($type === 'b') {
            return $value === '1' ? 'TRUE' : 'FALSE';
        }

        return $value;
    }

    private function extractDelimitedRows($filePath) {
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            return [];
        }

        $rows = [];
        while (($row = fgetcsv($handle)) !== false) {
            $rows[] = $row;
        }
        fclose($handle);

        return $rows;
    }

    private function excelColumnIndex($cellRef) {
        $letters = preg_replace('/[^A-Z]/', '', strtoupper($cellRef));
        $index = 0;

        for ($i = 0; $i < strlen($letters); $i++) {
            $index = ($index * 26) + (ord($letters[$i]) - 64);
        }

        return max(0, $index - 1);
    }

    private function normalizeHeaders($row) {
        $headers = [];
        foreach ($row as $index => $header) {
            $header = trim((string) $header);
            $headers[] = $header !== '' ? $header : 'column_' . ($index + 1);
        }

        return $headers;
    }

    private function isEmptyRow($row) {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    private function insertExtractedData($uploadId, $rowNumber, $rawData) {
        $rawDataJson = $this->conn->escape_string(json_encode($rawData, JSON_UNESCAPED_UNICODE));
        $sql = "INSERT INTO extracted_data (upload_id, row_number, raw_data, validated_data, is_valid) 
                VALUES ($uploadId, $rowNumber, '$rawDataJson', '$rawDataJson', true)";
        $this->conn->query($sql);
    }

    private function parseWordTables($content) {
        // Simple table parsing - split by delimiters
        $tables = [];
        $lines = explode("\n", $content);
        $currentTable = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line) {
                $cells = array_map('trim', explode("\t", $line));
                $currentTable[] = array_combine(
                    range(1, count($cells)),
                    $cells
                );
            }
        }

        if (!empty($currentTable)) {
            $tables[] = $currentTable;
        }

        return $tables;
    }

    private function logProcessing($uploadId, $action, $details, $status) {
        $sql = "INSERT INTO processing_log (upload_id, action, details, status) 
                VALUES ($uploadId, '$action', '" . $this->conn->escape_string($details) . "', '$status')";
        $this->conn->query($sql);
    }

    public function importToDatabase($uploadId, $targetTable) {
        if (!$this->isSafeIdentifier($targetTable)) {
            return ['success' => false, 'message' => 'Invalid target table name'];
        }

        // Get extracted data
        $result = $this->conn->query("SELECT id, raw_data FROM extracted_data WHERE upload_id = $uploadId AND is_valid = true");
        
        $importedCount = 0;
        $failedCount = 0;

        while ($row = $result->fetch_assoc()) {
            $data = json_decode($row['raw_data'], true);
            
            // Build INSERT query for target table
            $filteredData = [];
            foreach ($data as $column => $value) {
                if ($this->isSafeIdentifier($column)) {
                    $filteredData[$column] = $value;
                }
            }

            if (empty($filteredData) || count($filteredData) !== count($data)) {
                $failedCount++;
                continue;
            }

            $cols = array_keys($filteredData);
            $vals = array_values($filteredData);
            
            $colList = '`' . implode('`, `', $cols) . '`';
            $valList = "'" . implode("', '", array_map([$this->conn, 'escape_string'], $vals)) . "'";
            
            $sql = "INSERT INTO `$targetTable` ($colList) VALUES ($valList)";
            
            if ($this->conn->query($sql)) {
                $importedCount++;
            } else {
                $failedCount++;
            }
        }

        // Record import
        $sql = "INSERT INTO data_imports (upload_id, target_table, total_records, imported_records, failed_records, import_status) 
                VALUES ($uploadId, '" . $this->conn->escape_string($targetTable) . "', " . ($importedCount + $failedCount) . ", $importedCount, $failedCount, 'completed')";
        $this->conn->query($sql);

        return ['success' => true, 'imported' => $importedCount, 'failed' => $failedCount];
    }

    private function isSafeIdentifier($identifier) {
        return is_string($identifier) && preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $identifier);
    }
}
?>
