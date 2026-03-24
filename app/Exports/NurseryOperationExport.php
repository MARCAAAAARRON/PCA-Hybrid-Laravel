<?php

namespace App\Exports;

use App\Models\NurseryOperation;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class NurseryOperationExport
{
    protected array $records;
    protected $site;
    protected Carbon $asOfDate;

    public function __construct(iterable $records)
    {
        // Ensure we have batches and varieties loaded
        if ($records instanceof \Illuminate\Database\Eloquent\Builder) {
            $this->records = $records->with(['batches.varieties', 'fieldSite'])->get()->all();
        } else {
            $this->records = is_array($records) ? $records : $records->all();
        }
        
        $this->site = count($this->records) > 0 ? $this->records[0]->fieldSite : null;
        $this->asOfDate = now();
    }

    public function export()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Nursery Operations');

        $this->setupPage($sheet);
        $this->drawHeader($sheet);
        $this->drawTableHeaders($sheet);
        $currentRow = $this->drawData($sheet);
        $this->drawFooter($sheet, $currentRow + 2);

        $writer = new Xlsx($spreadsheet);
        
        $fileName = 'Nursery_Operations_' . str_replace(' ', '_', $this->site?->name ?? 'Export') . '_' . now()->format('Y-m-d') . '.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), 'export_nursery');
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

        $mergeEnd = 'R';
        
        $sheet->mergeCells("A1:{$mergeEnd}1");
        $sheet->setCellValue('A1', 'COCONUT HYBRIDIZATION PROGRAM-CFIDP');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells("A2:{$mergeEnd}2");
        $sheet->setCellValue('A2', 'COMMUNAL NURSERY ESTABLISHMENT');
        $sheet->getStyle('A2')->getFont()->setSize(10);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $isTerminal = count($this->records) > 0 && $this->records[0]->report_type === 'terminal';
        $reportTitle = $isTerminal ? 'Communal Nursery Establishment Terminal Report' : 'Communal Nursery Establishment Monthly Report';

        $sheet->mergeCells("A3:{$mergeEnd}3");
        $sheet->setCellValue('A3', $reportTitle);
        $sheet->getStyle('A3')->getFont()->setSize(10);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $asOfStr = 'as of ' . $this->asOfDate->format('F d, Y');
        $sheet->mergeCells("A4:{$mergeEnd}4");
        $sheet->setCellValue('A4', $asOfStr);
        $sheet->getStyle('A4')->getFont()->setSize(10)->setUnderline(true);
        $sheet->getStyle('A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }

    protected function drawTableHeaders(Worksheet $sheet)
    {
        $headers = [
            'Region / Province / District',  // A
            'Barangay / Municipality',        // B
            'Entity Name',                     // C
            'Representative',                 // D
            'Target No. of Seednuts',         // E
            'No. of Seednuts Harvested',      // F
            'Date Harvested',                 // G
            'Date Seednuts Received',         // H
            'Source of Seednuts',             // I
            'Type/Variety',                   // J
            'No. of Seednuts Sown',          // K
            'Date Seednut Sown',             // L
            'No. of Seedlings Germinated',   // M
            'No. of Ungerminated Seednuts',  // N
            'No. of Culled Seedlings',       // O
            'No. of Good Seedlings @ 1 ft',  // P
            'No. of Ready to Plant (Polybagged)',  // Q
            'No. of Seedlings Dispatched',   // R
        ];

        $col = 1;
        foreach ($headers as $h) {
            $sheet->setCellValueByColumnAndRow($col, 5, $h);
            $col++;
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

        $sheet->getStyle('A5:R5')->applyFromArray($styleArray);
    }

    protected function drawData(Worksheet $sheet)
    {
        $row = 6;
        
        foreach ($this->records as $rec) {
            $startRow = $row;
            $hasData = false;

            foreach ($rec->batches as $batch) {
                foreach ($batch->varieties as $v) {
                    $hasData = true;
                    $sheet->setCellValue('A' . $row, $rec->region_province_district);
                    $sheet->setCellValue('B' . $row, $rec->barangay_municipality);
                    $sheet->setCellValue('C' . $row, $rec->proponent_entity);
                    $sheet->setCellValue('D' . $row, $rec->proponent_representative);
                    $sheet->setCellValue('E' . $row, $rec->target_seednuts);
                    
                    $sheet->setCellValue('F' . $row, $batch->seednuts_harvested);
                    $sheet->setCellValue('G' . $row, $batch->date_harvested?->format('m/d/Y') ?? '');
                    $sheet->setCellValue('H' . $row, $batch->date_received?->format('m/d/Y') ?? '');
                    $sheet->setCellValue('I' . $row, $batch->source_of_seednuts);
                    
                    $sheet->setCellValue('J' . $row, $v->variety);
                    $sheet->setCellValue('K' . $row, $v->seednuts_sown);
                    $sheet->setCellValue('L' . $row, $v->date_sown?->format('m/d/Y') ?? '');
                    $sheet->setCellValue('M' . $row, $v->seedlings_germinated);
                    $sheet->setCellValue('N' . $row, $v->ungerminated_seednuts);
                    $sheet->setCellValue('O' . $row, $v->culled_seedlings);
                    $sheet->setCellValue('P' . $row, $v->good_seedlings);
                    $sheet->setCellValue('Q' . $row, $v->ready_to_plant);
                    $sheet->setCellValue('R' . $row, $v->seedlings_dispatched);
                    
                    $sheet->getStyle("A$row:R$row")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                    $row++;
                }
            }
            
            if (!$hasData) {
                // If no batches/varieties, at least show the operation details
                $sheet->setCellValue('A' . $row, $rec->region_province_district);
                $sheet->setCellValue('B' . $row, $rec->barangay_municipality);
                $sheet->setCellValue('C' . $row, $rec->proponent_entity);
                $sheet->setCellValue('D' . $row, $rec->proponent_representative);
                $sheet->setCellValue('E' . $row, $rec->target_seednuts);
                $sheet->getStyle("A$row:R$row")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $row++;
            } else {
                // Merge parent cells for columns A-E if multiple rows were created
                $endRow = $row - 1;
                if ($endRow > startRow) {
                    foreach (range('A', 'E') as $colID) {
                        $sheet->mergeCells("{$colID}{$startRow}:{$colID}{$endRow}");
                        $sheet->getStyle("{$colID}{$startRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                    }
                }
            }
        }

        $this->autoSizeColumns($sheet);
        return $row;
    }

    protected function drawFooter(Worksheet $sheet, $startRow)
    {
        $row = $startRow;
        
        $sheet->setCellValue('A' . $row, 'Prepared by:');
        $sheet->setCellValue('H' . $row, 'Reviewed by:');
        $sheet->setCellValue('O' . $row, 'Noted by:');
        
        $row += 4;
        
        $prepName = auth()->user()->name ?? 'ROSITA J. MIASCO';
        $prepTitle = auth()->user()->role ?? 'COS/Agriculturist';
        
        $sheet->setCellValue('A' . $row, strtoupper($prepName));
        $sheet->setCellValue('H' . $row, 'ALVIN B. CUBIBA');
        $sheet->setCellValue('O' . $row, 'JOVENCIO G. FEUDILOA');
        
        $sheet->getStyle("A$row:R$row")->getFont()->setBold(true);
        $sheet->getStyle("A$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("H$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("O$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        $row++;
        $sheet->setCellValue('A' . $row, $prepTitle);
        $sheet->setCellValue('H' . $row, 'Senior Agriculturist');
        $sheet->setCellValue('O' . $row, 'PCDM/Division Chief I');
        
        $sheet->getStyle("A$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("H$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("O$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }

    protected function autoSizeColumns(Worksheet $sheet)
    {
        foreach (range('A', 'R') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
    }
}
