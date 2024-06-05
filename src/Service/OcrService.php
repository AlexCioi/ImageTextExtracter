<?php

namespace App\Service;

use thiagoalessio\TesseractOCR\TesseractOCR;

class OcrService
{
    public function extractTextFromImage(string $imagePath): string
    {
        $ocr = new TesseractOCR();
        $ocr->image($imagePath);
        return $ocr->run();
    }
}
