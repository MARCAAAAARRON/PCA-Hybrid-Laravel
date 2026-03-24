<?php

namespace App\Exports;

use App\Models\PollenProduction;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PollenProductionExport
{
    protected array $records;
    protected $site;
    protected Carbon $asOfDate;

    public function __construct(iterable $records)
    {
        $this->records = is_array($records) ? $records : $records->all();
        $this->site = count($this->records) > 0 ? $this->records[0]->fieldSite : null;
        $this->asOfDate = now();
    }

    public function export()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Pollen Production');

        $this->setupPage($sheet);
        $this->drawHeader($sheet);
        $this->drawTableHeaders($sheet);
        $currentRow = $this->drawData($sheet);
        $this->drawFooter($sheet, $currentRow + 2);

        $writer = new Xlsx($spreadsheet);
        
        $fileName = 'Pollen_Production_' . str_replace(' ', '_', $this->site?->name ?? 'Export') . '_' . now()->format('Y-m-d') . '.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), 'export_pollen');
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }

    protected function setupPage(Worksheet $sheet)
    {
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_LETTER);
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);
    }

    protected function drawHeader(Worksheet $sheet)
    {
        // Logo
        $logoPath = public_path('images/PCA_DA_Logo.png');
        if (file_exists($logoPath)) {
            $drawing = new Drawing();
            $drawing->setName('PCA Logo');
            $drawing->setPath($logoPath);
            $drawing->setHeight(75);
            $drawing->setCoordinates('B1');
            $drawing->setWorksheet($sheet);
        }

        $mergeEnd = 'L';
        
        $sheet->mergeCells("A1:{$mergeEnd}1");
        $sheet->setCellValue('A1', 'COCONUT HYBRIDIZATION PROGRAM-CFIDP');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells("A2:{$mergeEnd}2");
        $sheet->setCellValue('A2', 'POLLEN PRODUCTION');
        $sheet->getStyle('A2')->getFont()->setSize(10);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheetName = $this->site?->name ?? 'All Sites';
        $sheet->mergeCells("A3:{$mergeEnd}3");
        $sheet->setCellValue('A3', "Pollen Production and Inventory Monthly Report — {$sheetName}");
        $sheet->getStyle('A3')->getFont()->setSize(10);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $asOfStr = 'as of ' . $this->asOfDate->format('F d, Y');
        $sheet->mergeCells("A4:{$mergeEnd}4");
        $sheet->setCellValue('A4', $asOfStr);
        $sheet->getStyle('A4')->getFont()->setSize(10)->setUnderline(true);
        $sheet->getStyle('A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Center/Unit info
        $centerText = $this->site?->name ?? 'Unknown';
        if (str_contains(strtolower($centerText), 'loay')) {
            $centerText = 'LOAY CODE FARM, LAS SALINAS SUR, LOAY, BOHOL';
        }
        
        $sheet->mergeCells("A6:{$mergeEnd}6");
        $sheet->setCellValue('A6', "CENTER/UNIT: {$centerText}");
        $sheet->getStyle('A6')->getFont()->setBold(true);

        $pollenVar = count($this->records) > 0 ? $this->records[0]->pollen_variety : '';
        $sheet->mergeCells("A7:{$mergeEnd}7");
        $sheet->setCellValue('A7', "POLLEN VARIETY: {$pollenVar}");
        $sheet->getStyle('A7')->getFont()->setBold(true);
    }

    protected function drawTableHeaders(Worksheet $sheet)
    {
        $headers9 = [
            'A9' => 'MONTH',
            'B9' => "Ending Balance\nLast Month\n(g Pollens)",
            'C9' => 'POLLENS RECEIVED FROM OTHER CENTER',
            'F9' => 'POLLEN UTILIZATION (grams of Pollen) per Week',
            'L9' => "Ending Balance\n(g Pollens)",
        ];

        foreach ($headers9 as $cell => $val) {
            $sheet->setCellValue($cell, $val);
        }

        $headers10 = [
            'C10' => 'Source',
            'D10' => "Date Received\nmm/dd/yyyy",
            'E10' => "Grams of\nPollens",
            'F10' => 'Week 1',
            'G10' => 'Week 2',
            'H10' => 'Week 3',
            'I10' => 'Week 4',
            'J10' => 'Week 5',
            'K10' => 'TOTAL',
        ];

        foreach ($headers10 as $cell => $val) {
            $sheet->setCellValue($cell, $val);
        }

        $styleArray = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0B9E4F'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];

        $sheet->getStyle('A9:L10')->applyFromArray($styleArray);
        
        $sheet->mergeCells('A9:A10');
        $sheet->mergeCells('B9:B10');
        $sheet->mergeCells('C9:E9');
        $sheet->mergeCells('F9:K9');
        $sheet->mergeCells('L9:L10');
    }

    protected function drawData(Worksheet $sheet)
    {
        $row = 11;
        $totalReceived = 0;
        $totalUtil = 0;

        foreach ($this->records as $rec) {
            $sheet->setCellValue('A' . $row, $rec->month_label);
            $sheet->setCellValue('B' . $row, $rec->ending_balance_prev > 0 ? "{$rec->ending_balance_prev} g" : '');
            $sheet->setCellValue('C' . $row, $rec->pollen_source);
            $sheet->setCellValue('D' . $row, $rec->date_received?->format('m/d/Y') ?? '');
            $sheet->setCellValue('E' . $row, $rec->pollens_received > 0 ? "{$rec->pollens_received} g" : '');
            $sheet->setCellValue('F' . $row, $rec->week1 > 0 ? "{$rec->week1} g" : '');
            $sheet->setCellValue('G' . $row, $rec->week2 > 0 ? "{$rec->week2} g" : '');
            $sheet->setCellValue('H' . $row, $rec->week3 > 0 ? "{$rec->week3} g" : '');
            $sheet->setCellValue('I' . $row, $rec->week4 > 0 ? "{$rec->week4} g" : '');
            $sheet->setCellValue('J' . $row, $rec->week5 > 0 ? "{$rec->week5} g" : '');
            $sheet->setCellValue('K' . $row, $rec->total_utilization > 0 ? "{$rec->total_utilization} g" : '');
            $sheet->setCellValue('L' . $row, $rec->ending_balance > 0 ? "{$rec->ending_balance} g" : '');
            
            $totalReceived += (float)$rec->pollens_received;
            $totalUtil += (float)$rec->total_utilization;

            $sheet->getStyle("A$row:L$row")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $row++;
        }

        // Total Row
        $sheet->setCellValue('A' . $row, 'TOTAL:');
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        
        if ($totalReceived > 0) {
            $sheet->setCellValue('E' . $row, $this->formatWeight($totalReceived));
        }
        if ($totalUtil > 0) {
            $sheet->setCellValue('K' . $row, $this->formatWeight($totalUtil));
        }

        $sheet->getStyle("A$row:L$row")->getFont()->setBold(true);
        $sheet->getStyle("A$row:L$row")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        $this->autoSizeColumns($sheet);
        return $row;
    }

    protected function formatWeight($grams)
    {
        if ($grams >= 1000) {
            return number_format($grams / 1000, 2) . ' kg';
        }
        return number_format($grams, 2) . ' g';
    }

    protected function drawFooter(Worksheet $sheet, $startRow)
    {
        $row = $startRow;
        
        $sheet->setCellValue('A' . $row, 'Prepared by:');
        $sheet->setCellValue('F' . $row, 'Reviewed by:');
        $sheet->setCellValue('I' . $row, 'Noted by:');
        
        $row += 4;
        
        $prepName = auth()->user()->name ?? 'ROSITA J. MIASCO';
        $prepTitle = auth()->user()->role ?? 'COS/Agriculturist';
        
        $sheet->setCellValue('A' . $row, strtoupper($prepName));
        $sheet->setCellValue('F' . $row, 'ALVIN B. CUBIBA');
        $sheet->setCellValue('I' . $row, 'JOVENCIO G. FEUDILOA');
        
        $sheet->getStyle("A$row")->getFont()->setBold(true);
        $sheet->getStyle("A$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("F$row")->getFont()->setBold(true);
        $sheet->getStyle("F$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("I$row")->getFont()->setBold(true);
        $sheet->getStyle("I$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        $row++;
        $sheet->setCellValue('A' . $row, $prepTitle);
        $sheet->setCellValue('F' . $row, 'Senior Agriculturist');
        $sheet->setCellValue('I' . $row, 'PCDM/Division Chief I');
        
        $sheet->getStyle("A$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("F$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("I$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }

    protected function autoSizeColumns(Worksheet $sheet)
    {
        foreach (range('A', 'L') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
    }
}
