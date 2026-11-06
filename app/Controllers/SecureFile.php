<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class SecureFile extends BaseController
{
    /**
     * Serve ID card images for both players and coaches
     * (Stored unified in writable/secure_uploads/id_cards/)
     */
    public function idCard($filename)
    {
        // Basic auth check
        if (!session()->get('isLoggedIn')) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        // Prevent path traversal
        $filename = basename($filename);
        
        // Define file path
        $filePath = WRITEPATH . 'secure_uploads/id_cards/' . $filename;

        if (!file_exists($filePath)) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'File not found']);
        }

        $mimeType = mime_content_type($filePath);
        
        return $this->response
            ->setHeader('Content-Type', $mimeType)
            ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
            ->setBody(file_get_contents($filePath));
    }

// ใน IdCardController.php
    public function show($filename = null)
    {
        die("Controller ทำงานแล้ว! กำลังเรียกไฟล์: " . $filename);
    }

}
