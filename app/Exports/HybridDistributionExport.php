<?php

namespace App\Exports;

use App\Models\HybridDistribution;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class HybridDistributionExport
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
        $sheet->setTitle('Hybrid Distribution');

        $this->setupPage($sheet);
        $this->drawHeader($sheet);
        $this->drawTableHeaders($sheet);
        $currentRow = $this->drawData($sheet);
        $this->drawFooter($sheet, $currentRow + 2);

        $writer = new Xlsx($spreadsheet);
        
        $fileName = 'Hybrid_Distribution_' . str_replace(' ', '_', $this->site?->name ?? 'Export') . '_' . now()->format('Y-m-d') . '.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), 'export_dist');
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

        $mergeEnd = 'S';
        
        $sheet->mergeCells("A1:{$mergeEnd}1");
        $sheet->setCellValue('A1', 'Department of Agriculture');
        $sheet->getStyle('A1')->getFont()->setSize(10);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells("A2:{$mergeEnd}2");
        $sheet->setCellValue('A2', 'PHILIPPINE COCONUT AUTHORITY');
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells("A3:{$mergeEnd}3");
        $sheet->setCellValue('A3', 'REGION VII');
        $sheet->getStyle('A3')->getFont()->setSize(10);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $asOfStr = 'as of ' . $this->asOfDate->format('F d, Y');
        $sheet->mergeCells("A4:{$mergeEnd}4");
        $sheet->setCellValue('A4', $asOfStr);
        $sheet->getStyle('A4')->getFont()->setSize(10)->setUnderline(true);
        $sheet->getStyle('A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells("A5:{$mergeEnd}5");
        $sheet->setCellValue('A5', 'COCONUT HYBRIDIZATION PROGRAM');
        $sheet->getStyle('A5')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells("A6:{$mergeEnd}6");
        $sheet->setCellValue('A6', 'COMMUNAL NURSERY: DISPATCHED SEEDLINGS');
        $sheet->getStyle('A6')->getFont()->setSize(10);
        $sheet->getStyle('A6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }

    protected function drawTableHeaders(Worksheet $sheet)
    {
        // Main Headers Row 8
        $headers8 = [
            'A8' => 'Region',
            'B8' => 'Province',
            'C8' => 'District',
            'D8' => 'Municipality',
            'E8' => 'Barangay',
            'F8' => 'Name of Farmer Participant',
            'I8' => 'Gender',
            'K8' => 'Farm Location',
            'N8' => 'Seedlings Received',
            'O8' => 'Date Received',
            'P8' => 'Type/Variety',
            'Q8' => 'No. of Seedlings Planted',
            'R8' => 'Date Planted',
            'S8' => 'REMARKS',
        ];

        foreach ($headers8 as $cell => $val) {
            $sheet->setCellValue($cell, $val);
        }

        // Subheaders Row 9 (Family Name, Given Name, M.I.)
        $sheet->setCellValue('F9', 'Family Name');
        $sheet->setCellValue('G9', 'Given Name');
        $sheet->setCellValue('H9', 'M.I.');

        // Subheaders Row 10 (Gender and Farm Location detailed)
        $sheet->setCellValue('I10', 'Male');
        $sheet->setCellValue('J10', 'Female');
        $sheet->setCellValue('K10', 'Barangay');
        $sheet->setCellValue('L10', 'Municipality');
        $sheet->setCellValue('M10', 'Province');

        // Styling
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

        $sheet->getStyle('A8:S10')->applyFromArray($styleArray);
        
        // Merging main headers
        $sheet->mergeCells('A8:A10');
        $sheet->mergeCells('B8:B10');
        $sheet->mergeCells('C8:C10');
        $sheet->mergeCells('D8:D10');
        $sheet->mergeCells('E8:E10');
        $sheet->mergeCells('F8:H8');
        $sheet->mergeCells('F9:F10');
        $sheet->mergeCells('G9:G10');
        $sheet->mergeCells('H9:H10');
        $sheet->mergeCells('I8:J8');
        $sheet->mergeCells('I9:I9'); // Male placeholder merge? Actually let's just make it better
        $sheet->mergeCells('I9:J9');
        $sheet->mergeCells('K8:M8');
        $sheet->mergeCells('K9:M9');
        $sheet->mergeCells('N8:N10');
        $sheet->mergeCells('O8:O10');
        $sheet->mergeCells('P8:P10');
        $sheet->mergeCells('Q8:Q10');
        $sheet->mergeCells('R8:R10');
        $sheet->mergeCells('S8:S10');

        // BOHOL PROVINCE separator
        $sheet->mergeCells('A11:S11');
        $sheet->setCellValue('A11', 'BOHOL PROVINCE');
        $sheet->getStyle('A11')->getFont()->setBold(true);
        $sheet->getStyle('A11')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A12:S12');
        $sheet->setCellValue('A12', 'COMMUNAL NURSERY AT ' . strtoupper($this->site?->name ?? 'ALL SITES'));
        $sheet->getStyle('A12')->getFont()->setBold(true);
        $sheet->getStyle('A12')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }

    protected function drawData(Worksheet $sheet)
    {
        $row = 13;
        $totalPlanted = 0;
        $totalReceived = 0;

        foreach ($this->records as $rec) {
            $sheet->setCellValue('A' . $row, $rec->region);
            $sheet->setCellValue('B' . $row, $rec->province);
            $sheet->setCellValue('C' . $row, $rec->district);
            $sheet->setCellValue('D' . $row, $rec->municipality);
            $sheet->setCellValue('E' . $row, $rec->barangay);
            $sheet->setCellValue('F' . $row, $rec->farmer_last_name);
            $sheet->setCellValue('G' . $row, $rec->farmer_first_name);
            $sheet->setCellValue('H' . $row, $rec->farmer_middle_initial);
            
            $isMale = strtolower($rec->gender ?? '') === 'male' || $rec->is_male;
            $sheet->setCellValue('I' . $row, $isMale ? '/' : '');
            $sheet->setCellValue('J' . $row, !$isMale ? '/' : '');
            
            $sheet->setCellValue('K' . $row, $rec->farm_barangay);
            $sheet->setCellValue('L' . $row, $rec->farm_municipality);
            $sheet->setCellValue('M' . $row, $rec->farm_province);
            $sheet->setCellValue('N' . $row, $rec->seedlings_received);
            $sheet->setCellValue('O' . $row, $rec->date_received?->format('m/d/Y') ?? '');
            $sheet->setCellValue('P' . $row, $rec->variety);
            $sheet->setCellValue('Q' . $row, $rec->seedlings_planted);
            $sheet->setCellValue('R' . $row, $rec->date_planted?->format('m/d/Y') ?? '');
            $sheet->setCellValue('S' . $row, $rec->remarks);
            
            $totalReceived += (int)$rec->seedlings_received;
            $totalPlanted += (int)$rec->seedlings_planted;

            $sheet->getStyle("A$row:S$row")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $row++;
        }

        // Total Row
        $sheet->setCellValue('F' . $row, 'TOTAL:');
        $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('N' . $row, $totalReceived);
        $sheet->setCellValue('Q' . $row, $totalPlanted);
        
        $sheet->getStyle("A$row:S$row")->getFont()->setBold(true);
        $sheet->getStyle("A$row:S$row")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        $this->autoSizeColumns($sheet);
        return $row;
    }

    protected function drawFooter(Worksheet $sheet, $startRow)
    {
        $row = $startRow;
        
        $sheet->setCellValue('A' . $row, 'Prepared by:');
        $sheet->setCellValue('H' . $row, 'Reviewed by:');
        $sheet->setCellValue('P' . $row, 'Noted by:');
        
        $row += 4;
        
        $prepName = auth()->user()->name ?? 'ROSITA J. MIASCO';
        $prepTitle = auth()->user()->role ?? 'COS/Agriculturist';
        
        $sheet->setCellValue('A' . $row, strtoupper($prepName));
        $sheet->setCellValue('H' . $row, 'ALVIN B. CUBIBA');
        $sheet->setCellValue('P' . $row, 'JOVENCIO G. FEUDILOA');
        
        $sheet->getStyle("A$row:S$row")->getFont()->setBold(true);
        $sheet->getStyle("A$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("H$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("P$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        $row++;
        $sheet->setCellValue('A' . $row, $prepTitle);
        $sheet->setCellValue('H' . $row, 'Senior Agriculturist');
        $sheet->setCellValue('P' . $row, 'PCDM/Division Chief I');
        
        $sheet->getStyle("A$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("H$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("P$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }

    protected function autoSizeColumns(Worksheet $sheet)
    {
        foreach (range('A', 'S') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
    }
}
