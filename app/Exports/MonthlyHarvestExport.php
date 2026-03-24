<?php

namespace App\Exports;

use App\Models\MonthlyHarvest;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MonthlyHarvestExport
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
        $sheet->setTitle('Monthly Harvest');

        $this->setupPage($sheet);
        $this->drawHeader($sheet);
        $this->drawTableHeaders($sheet);
        $currentRow = $this->drawData($sheet);
        $this->drawFooter($sheet, $currentRow + 2);

        $writer = new Xlsx($spreadsheet);
        
        $fileName = 'Monthly_Harvest_' . str_replace(' ', '_', $this->site?->name ?? 'Export') . '_' . now()->format('Y-m-d') . '.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), 'export');
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

        $mergeEnd = 'U';
        
        $sheet->mergeCells("A1:{$mergeEnd}1");
        $sheet->setCellValue('A1', 'PHILIPPINE COCONUT AUTHORITY');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells("A2:{$mergeEnd}2");
        $sheet->setCellValue('A2', 'COCONUT HYBRIDIZATION PROJECT-CFIDP');
        $sheet->getStyle('A2')->getFont()->setSize(10);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells("A3:{$mergeEnd}3");
        $sheet->setCellValue('A3', 'ON-FARM HYBRID SEEDNUT PRODUCTION');
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
            'A5' => 'Farm Location',
            'B5' => 'Name of Partner',
            'C5' => 'Area (Ha.)',
            'D5' => 'Age of Palms (Years)',
            'E5' => 'No. of Hybridized Palms',
            'F5' => 'Variety / Hybrid Crosses',
            'G5' => 'Seednuts Produced',
            'H5' => 'Monthly Production (No. of Seednuts)',
            'T5' => 'TOTAL',
            'U5' => 'Remarks',
        ];

        foreach ($headers as $cell => $val) {
            $sheet->setCellValue($cell, $val);
        }

        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $col = 'H';
        foreach ($months as $month) {
            $sheet->setCellValue($col . '6', $month);
            $col++;
        }

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

        $sheet->getStyle('A5:U6')->applyFromArray($styleArray);
        
        // Merging main headers
        $sheet->mergeCells('A5:A6');
        $sheet->mergeCells('B5:B6');
        $sheet->mergeCells('C5:C6');
        $sheet->mergeCells('D5:D6');
        $sheet->mergeCells('E5:E6');
        $sheet->mergeCells('F5:F6');
        $sheet->mergeCells('G5:G6');
        $sheet->mergeCells('H5:S5');
        $sheet->mergeCells('T5:T6');
        $sheet->mergeCells('U5:U6');
    }

    protected function drawData(Worksheet $sheet)
    {
        $row = 7;
        
        // Group by Farm (location + farm_name)
        $farms = [];
        foreach ($this->records as $rec) {
            $key = ($rec->location ?? '') . '|' . ($rec->farm_name ?? '');
            if (!isset($farms[$key])) {
                $farms[$key] = [
                    'location' => $rec->location,
                    'farm_name' => $rec->farm_name,
                    'area_ha' => $rec->area_ha,
                    'age_of_palms' => $rec->age_of_palms,
                    'num_hybridized_palms' => $rec->num_hybridized_palms,
                    'varieties' => [],
                ];
            }
            
            foreach ($rec->varieties as $v) {
                $varKey = ($v->variety ?? '') . '|' . ($v->seednuts_type ?? '');
                if (!isset($farms[$key]['varieties'][$varKey])) {
                    $farms[$key]['varieties'][$varKey] = [
                        'variety' => $v->variety,
                        'seednuts_type' => $v->seednuts_type,
                        'months' => array_fill(1, 12, 0),
                        'remarks' => $v->remarks,
                    ];
                }
                $month = $rec->report_month->month;
                $farms[$key]['varieties'][$varKey]['months'][$month] += $v->seednuts_count;
            }
        }

        $grandTotalArea = 0;
        $grandTotalPalms = 0;
        $monthTotals = array_fill(1, 12, 0);

        foreach ($farms as $farm) {
            $firstVarRow = $row;
            foreach ($farm['varieties'] as $v) {
                if ($row === $firstVarRow) {
                    $sheet->setCellValue('A' . $row, $farm['location']);
                    $sheet->setCellValue('B' . $row, $farm['farm_name']);
                    $sheet->setCellValue('C' . $row, $farm['area_ha']);
                    $sheet->setCellValue('D' . $row, $farm['age_of_palms']);
                    $sheet->setCellValue('E' . $row, $farm['num_hybridized_palms']);
                    
                    $grandTotalArea += (float)$farm['area_ha'];
                    $grandTotalPalms += (int)$farm['num_hybridized_palms'];
                }
                
                $sheet->setCellValue('F' . $row, $v['variety']);
                $sheet->setCellValue('G' . $row, $v['seednuts_type']);
                
                $varTotal = 0;
                $col = 'H';
                for($m = 1; $m <= 12; $m++) {
                    $count = $v['months'][$m];
                    if ($count > 0) {
                        $sheet->setCellValue($col . $row, $count);
                        $monthTotals[$m] += $count;
                        $varTotal += $count;
                    }
                    $col++;
                }
                
                $sheet->setCellValue('T' . $row, $varTotal);
                $sheet->setCellValue('U' . $row, $v['remarks']);
                
                $this->applyRowBorder($sheet, $row);
                $row++;
            }
        }

        // Total Row
        $sheet->setCellValue('B' . $row, 'TOTAL');
        $sheet->setCellValue('C' . $row, $grandTotalArea);
        $sheet->setCellValue('E' . $row, $grandTotalPalms);
        
        $col = 'H';
        $finalGrandTotal = 0;
        for($m = 1; $m <= 12; $m++) {
            if ($monthTotals[$m] > 0) {
                $sheet->setCellValue($col . $row, $monthTotals[$m]);
                $finalGrandTotal += $monthTotals[$m];
            }
            $col++;
        }
        $sheet->setCellValue('T' . $row, $finalGrandTotal);
        
        $sheet->getStyle("A$row:U$row")->getFont()->setBold(true);
        $this->applyRowBorder($sheet, $row);

        $this->autoSizeColumns($sheet);

        return $row;
    }

    protected function drawFooter(Worksheet $sheet, $startRow)
    {
        $row = $startRow;
        
        $sheet->setCellValue('A' . $row, 'Prepared by:');
        $sheet->setCellValue('K' . $row, 'Reviewed by:');
        $sheet->setCellValue('S' . $row, 'Noted by:');
        
        $row += 4;
        
        // Placeholders or dynamic names
        $prepName = auth()->user()->name ?? 'ROSITA J. MIASCO';
        $prepTitle = auth()->user()->role ?? 'COS/Agriculturist';
        
        $sheet->setCellValue('A' . $row, strtoupper($prepName));
        $sheet->setCellValue('K' . $row, 'ALVIN B. CUBIBA');
        $sheet->setCellValue('S' . $row, 'JOVENCIO G. FEUDILOA');
        
        $sheet->getStyle("A$row:U$row")->getFont()->setBold(true);
        $sheet->getStyle("A$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("K$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("S$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        $row++;
        $sheet->setCellValue('A' . $row, $prepTitle);
        $sheet->setCellValue('K' . $row, 'Senior Agriculturist');
        $sheet->setCellValue('S' . $row, 'PCDM/Division Chief I');
        
        $sheet->getStyle("A$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("K$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("S$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }

    protected function applyRowBorder(Worksheet $sheet, $row)
    {
        $sheet->getStyle("A$row:U$row")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    }

    protected function autoSizeColumns(Worksheet $sheet)
    {
        foreach (range('A', 'U') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
    }
}
