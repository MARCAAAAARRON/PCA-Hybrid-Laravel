<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Monthly Harvest Report - {{ $record->report_month->format('F Y') }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #16a34a;
            padding-bottom: 20px;
        }
        .header h1 {
            margin: 5px 0;
            color: #16a34a;
            font-size: 24px;
            text-transform: uppercase;
        }
        .header h2 {
            margin: 5px 0;
            color: #555;
            font-size: 16px;
        }
        .header h3 {
            margin: 5px 0;
            color: #777;
            font-size: 14px;
            font-weight: normal;
        }
        
        .info-grid {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .info-grid td {
            padding: 5px;
            vertical-align: top;
        }
        .info-label {
            font-weight: bold;
            width: 130px;
        }
        .info-value {
            border-bottom: 1px solid #ccc;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            margin-top: 20px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        table.data-table th {
            background-color: #f3f4f6;
            color: #374151;
            font-weight: bold;
        }
        table.data-table .text-right {
            text-align: right;
        }
        table.data-table .text-center {
            text-align: center;
        }
        
        .total-row {
            background-color: #e5e7eb;
            font-weight: bold;
            font-size: 14px;
        }

        .signatures {
            margin-top: 60px;
            width: 100%;
        }
        .signature-block {
            width: 45%;
            display: inline-block;
            vertical-align: top;
        }
        .signature-img-container {
            height: 80px;
            margin-bottom: 5px;
            position: relative;
        }
        .signature-img {
            max-height: 80px;
            max-width: 200px;
            position: absolute;
            bottom: 0;
            left: 0;
        }
        .signatory-name {
            font-weight: bold;
            text-transform: uppercase;
            border-top: 1px solid #333;
            padding-top: 5px;
            display: inline-block;
            width: 80%;
        }
        .signatory-title {
            color: #666;
            font-size: 11px;
            margin-top: 2px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>REPUBLIC OF THE PHILIPPINES</h2>
        <h1>PHILIPPINE COCONUT AUTHORITY</h1>
        <h3>MONTHLY HYBRIDIZATION HARVEST REPORT</h3>
    </div>

    <table class="info-grid">
        <tr>
            <td class="info-label">Field Site:</td>
            <td class="info-value" colspan="3">{{ $record->fieldSite->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="info-label">Report Month:</td>
            <td class="info-value">{{ $record->report_month ? $record->report_month->format('F Y') : 'N/A' }}</td>
            <td class="info-label" style="width:100px;">Date Prepared:</td>
            <td class="info-value">{{ $record->date_prepared ? $record->date_prepared->format('M d, Y') : 'N/A' }}</td>
        </tr>
        <tr>
            <td class="info-label">Farm/Partner:</td>
            <td class="info-value">{{ $record->farm_name ?? 'N/A' }}</td>
            <td class="info-label">Location:</td>
            <td class="info-value">{{ $record->location ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="info-label">Area (Ha):</td>
            <td class="info-value">{{ $record->area_ha ?? 'N/A' }}</td>
            <td class="info-label">Age of Palms:</td>
            <td class="info-value">{{ $record->age_of_palms ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="info-label">Hybridized Palms:</td>
            <td class="info-value" colspan="3">{{ number_format($record->num_hybridized_palms) }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%">#</th>
                <th style="width: 50%">Variety / Hybrid Crosses</th>
                <th style="width: 20%">Type</th>
                <th style="width: 25%" class="text-right">Seednuts Produced</th>
            </tr>
        </thead>
        <tbody>
            @forelse($record->varieties as $index => $variety)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        {{ $variety->variety }}
                        @if($variety->remarks)
                            <div style="font-size: 10px; color: #666; margin-top: 4px;">* {{ $variety->remarks }}</div>
                        @endif
                    </td>
                    <td>{{ $variety->seednuts_type }}</td>
                    <td class="text-right">{{ number_format($variety->seednuts_count) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">No varieties recorded for this month.</td>
                </tr>
            @endforelse
            
            <tr class="total-row">
                <td colspan="3" class="text-right">TOTAL PRODUCTION:</td>
                <td class="text-right">{{ number_format($record->total_production) }}</td>
            </tr>
        </tbody>
    </table>

    @if($record->remarks)
        <div style="margin-top: 20px;">
            <strong>Remarks/Notes:</strong>
            <p style="margin-top: 5px; padding: 10px; background: #f9fafb; border: 1px solid #eee;">
                {{ $record->remarks }}
            </p>
        </div>
    @endif

    <!-- SIGNATURE BLOCK -->
    <div class="signatures">
        <div class="signature-block">
            <div style="color: #666; margin-bottom: 15px;">Prepared By:</div>
            
            <div class="signature-img-container">
                @if($record->preparedBy && $record->preparedBy->signature_image)
                    @php
                        $sigPath = storage_path('app/public/' . $record->preparedBy->signature_image);
                        // Convert image to base64 for reliable DomPDF rendering
                        if (file_exists($sigPath)) {
                            $type = pathinfo($sigPath, PATHINFO_EXTENSION);
                            $data = file_get_contents($sigPath);
                            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                            echo '<img src="' . $base64 . '" class="signature-img" alt="Signature">';
                        }
                    @endphp
                @endif
            </div>
            
            <div class="signatory-name">
                {{ $record->preparedBy ? ($record->preparedBy->first_name . ' ' . substr($record->preparedBy->middle_initial ?? '', 0, 1) . '. ' . $record->preparedBy->last_name) : '_______________________' }}
            </div>
            <div class="signatory-title">Site Supervisor</div>
        </div>

        <div class="signature-block" style="margin-left: 5%;">
            <div style="color: #666; margin-bottom: 15px;">Approved By:</div>
            
            <div class="signature-img-container">
                @if($record->reviewedBy && $record->reviewedBy->signature_image)
                    @php
                        $sigPath2 = storage_path('app/public/' . $record->reviewedBy->signature_image);
                        if (file_exists($sigPath2)) {
                            $type2 = pathinfo($sigPath2, PATHINFO_EXTENSION);
                            $data2 = file_get_contents($sigPath2);
                            $base64_2 = 'data:image/' . $type2 . ';base64,' . base64_encode($data2);
                            echo '<img src="' . $base64_2 . '" class="signature-img" alt="Signature">';
                        }
                    @endphp
                @endif
            </div>
            
            <div class="signatory-name">
                {{ $record->reviewedBy ? ($record->reviewedBy->first_name . ' ' . ltrim(substr($record->reviewedBy->middle_initial ?? '', 0, 1) . '.', '.') . ' ' . $record->reviewedBy->last_name) : '_______________________' }}
            </div>
            <div class="signatory-title">Project Manager / Administrator</div>
        </div>
    </div>
    
    <div style="margin-top: 50px; text-align: center; font-size: 10px; color: #9ca3af; border-top: 1px solid #eee; padding-top: 10px;">
        Generated by PCA Hybrid System on {{ now()->format('F d, Y h:i A') }}
    </div>

</body>
</html>
