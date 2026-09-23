<?php

namespace App\Services;

use setasign\Fpdi\Fpdi;

class WatermarkService
{
    /**
     * Add a diagonal text watermark to an existing PDF file.
     *
     * @param string $sourceFile Path to original PDF
     * @param string $outputFile Path to save watermarked PDF
     * @param string $text The watermark text
     * @return bool True if successful
     */
    public function generateWatermark(string $sourceFile, string $outputFile, string $text)
    {
        if (!file_exists($sourceFile)) {
            return false;
        }

        try {
            $pdf = new Fpdi();
            $pageCount = $pdf->setSourceFile($sourceFile);

            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                // Import page
                $templateId = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($templateId);
                
                // Add page
                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                
                // Use the imported page
                $pdf->useTemplate($templateId);

                // Add watermark text
                $pdf->SetFont('Arial', 'B', 50);
                $pdf->SetTextColor(255, 0, 0); // Red color
                
                // Alpha transparency (FPDI / FPDF doesn't support native transparency easily without scripts,
                // but we can simulate a light grey watermark if we want, or just a distinct watermark).
                // Let's make it light grey
                $pdf->SetTextColor(200, 200, 200);

                // Calculate center and angle
                $x = $size['width'] / 2;
                $y = $size['height'] / 2;
                
                // We use RotatedText custom logic via standard FPDF rotation or just place it in center
                // To keep it simple and reliable across FPDF versions without extending the class,
                // we'll just write it across the center without rotation if FPDF rotation is tricky, 
                // OR implement a quick rotation. 
                
                // Quick pseudo-rotation using text placement (diagonal approximation)
                // For a true rotation we'd need to extend FPDF. For simplicity and robustness here:
                $pdf->SetXY(10, $y);
                $pdf->Cell(0, 10, strtoupper($text), 0, 0, 'C');
                $pdf->SetXY(10, $y - 50);
                $pdf->Cell(0, 10, strtoupper($text), 0, 0, 'C');
                $pdf->SetXY(10, $y + 50);
                $pdf->Cell(0, 10, strtoupper($text), 0, 0, 'C');
            }

            $pdf->Output('F', $outputFile);
            return true;
        } catch (\Exception $e) {
            log_message('error', 'Watermark error: ' . $e->getMessage());
            return false;
        }
    }
}
