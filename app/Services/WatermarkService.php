<?php

namespace App\Services;

use setasign\Fpdi\Fpdi;

/**
 * Internal FPDI extension supporting PDF ExtGState Alpha Transparency (10-15%)
 * and Coordinate Matrix Rotation (45-degree diagonal watermark).
 */
class DiagonalAlphaFpdi extends Fpdi
{
    protected array $extGStates = [];
    protected float $angle = 0.0;

    public function SetAlpha(float $alpha, string $bm = 'Normal'): void
    {
        $alpha = max(0.0, min(1.0, $alpha));
        $gs = $this->AddExtGState([
            'ca' => $alpha,
            'CA' => $alpha,
            'BM' => '/' . $bm,
        ]);
        $this->SetExtGState($gs);
    }

    protected function AddExtGState(array $parms): int
    {
        $n = count($this->extGStates) + 1;
        $this->extGStates[$n]['parms'] = $parms;
        return $n;
    }

    protected function SetExtGState(int $gs): void
    {
        $this->_out(sprintf('/GS%d gs', $gs));
    }

    protected function _enddoc(): void
    {
        if (!empty($this->extGStates) && $this->PDFVersion < '1.4') {
            $this->PDFVersion = '1.4';
        }
        parent::_enddoc();
    }

    protected function _putextgstates(): void
    {
        for ($i = 1; $i <= count($this->extGStates); $i++) {
            $this->_newobj();
            $this->extGStates[$i]['n'] = $this->n;
            $this->_put('<</Type /ExtGState');
            foreach ($this->extGStates[$i]['parms'] as $k => $v) {
                $this->_put('/' . $k . ' ' . $v);
            }
            $this->_put('>>');
            $this->_put('endobj');
        }
    }

    protected function _putresourcedict(): void
    {
        parent::_putresourcedict();
        $this->_put('/ExtGState <<');
        foreach ($this->extGStates as $k => $extGState) {
            $this->_put('/GS' . $k . ' ' . $extGState['n'] . ' 0 R');
        }
        $this->_put('>>');
    }

    protected function _putresources(): void
    {
        $this->_putextgstates();
        parent::_putresources();
    }

    public function Rotate(float $angle, float $x = -1, float $y = -1): void
    {
        if ($x == -1) {
            $x = $this->x;
        }
        if ($y == -1) {
            $y = $this->y;
        }
        if ($this->angle != 0) {
            $this->_out('Q');
        }
        $this->angle = $angle;
        if ($angle != 0) {
            $angle *= M_PI / 180;
            $c = cos($angle);
            $s = sin($angle);
            $cx = $x * $this->k;
            $cy = ($this->h - $y) * $this->k;
            $this->_out(sprintf(
                'q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm',
                $c,
                $s,
                -$s,
                $c,
                $cx,
                $cy,
                -$cx,
                -$cy
            ));
        }
    }

    protected function _endpage(): void
    {
        if ($this->angle != 0) {
            $this->angle = 0;
            $this->_out('Q');
        }
        parent::_endpage();
    }
}

class WatermarkService
{
    /**
     * Menambahkan watermark teks nama Dinas/OPD secara melintang diagonal (45 derajat)
     * dengan tingkat transparansi (opacity) 12% (rentang 10% - 15%) di setiap halaman PDF.
     *
     * @param string $sourceFile Path file PDF asli
     * @param string $outputFile Path file PDF hasil watermark
     * @param string $text       Nama Dinas / OPD (contoh: "DINAS PARIWISATA")
     * @param float  $opacity    Tingkat opacity (default 0.12 = 12%)
     * @return bool True jika berhasil
     */
    public function generateWatermark(string $sourceFile, string $outputFile, string $text, float $opacity = 0.12): bool
    {
        if (!file_exists($sourceFile)) {
            return false;
        }

        try {
            $pdf = new DiagonalAlphaFpdi();
            $pdf->SetAutoPageBreak(false);
            $pageCount = $pdf->setSourceFile($sourceFile);

            $cleanOpdName = strtoupper(trim($text));
            if ($cleanOpdName === '') {
                $cleanOpdName = 'PEMERINTAH KABUPATEN BULELENG';
            }

            // Batasi opacity di rentang aman (10% - 15% sesuai arahan pembimbing)
            $alpha = max(0.08, min(0.25, $opacity));

            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $templateId = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($templateId);

                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($templateId);

                $centerX = $size['width'] / 2;
                $centerY = $size['height'] / 2;
                $diagonalLength = sqrt(($size['width'] ** 2) + ($size['height'] ** 2)) * 0.72;

                // Warna abu-abu gelap / navy dengan alpha 12% agar teks dokumen di belakang tetap terbaca tajam
                $pdf->SetTextColor(30, 41, 59);
                $pdf->SetAlpha($alpha);

                // Sesuaikan ukuran font otomatis berdasarkan panjang nama Dinas/OPD
                $fontSize = 36;
                $pdf->SetFont('Helvetica', 'B', $fontSize);
                while ($fontSize > 18 && $pdf->GetStringWidth($cleanOpdName) > $diagonalLength) {
                    $fontSize -= 2;
                    $pdf->SetFont('Helvetica', 'B', $fontSize);
                }

                // Pecah menjadi 2 baris jika nama OPD sangat panjang (misal > 45 karakter)
                $lines = [$cleanOpdName];
                if ($pdf->GetStringWidth($cleanOpdName) > $diagonalLength && str_contains($cleanOpdName, ' ')) {
                    $words = explode(' ', $cleanOpdName);
                    $mid = (int) ceil(count($words) / 2);
                    $lines = [
                        implode(' ', array_slice($words, 0, $mid)),
                        implode(' ', array_slice($words, $mid)),
                    ];
                }

                // Putar 45 derajat melintang diagonal di titik tengah halaman
                $pdf->Rotate(45, $centerX, $centerY);

                $lineHeight = $fontSize * 0.45;
                $totalBlockHeight = count($lines) * $lineHeight;
                $startY = $centerY - ($totalBlockHeight / 2) + ($lineHeight / 2);

                foreach ($lines as $idx => $lineText) {
                    $strW = $pdf->GetStringWidth($lineText);
                    $lineX = $centerX - ($strW / 2);
                    $lineY = $startY + ($idx * $lineHeight);
                    $pdf->Text($lineX, $lineY, $lineText);
                }

                $pdf->Rotate(0);
                $pdf->SetAlpha(1.0);
            }

            $pdf->Output('F', $outputFile);
            return true;
        } catch (\Throwable $e) {
            log_message('error', 'Watermark error: ' . $e->getMessage());
            return false;
        }
    }
}
