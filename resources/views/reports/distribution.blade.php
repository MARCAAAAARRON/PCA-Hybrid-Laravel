<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Hybrid Distribution Report</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9px; margin: 0; padding: 15px; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #2d6a2e; padding-bottom: 10px; }
        .header h1 { font-size: 14px; color: #2d6a2e; margin: 0; }
        .header h2 { font-size: 12px; color: #333; margin: 3px 0; }
        .header p { font-size: 9px; color: #666; margin: 2px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #2d6a2e; color: white; padding: 4px 6px; text-align: left; font-size: 8px; }
        td { padding: 3px 6px; border-bottom: 1px solid #ddd; font-size: 8px; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .footer { margin-top: 30px; font-size: 8px; }
        .signature-block { display: flex; justify-content: space-between; margin-top: 40px; }
        .signature-line { width: 30%; text-align: center; }
        .signature-line hr { border: none; border-top: 1px solid #333; margin-bottom: 3px; }
        .total-row td { font-weight: bold; background-color: #e8f5e9; border-top: 2px solid #2d6a2e; }
    </style>
</head>
<body>
    <div class="header">
        <h1>PHILIPPINE COCONUT AUTHORITY</h1>
        <h2>Hybrid Coconut Seedling Distribution Report</h2>
        <p>{{ $fieldSite }} | Year: {{ $year }} @if($month) | Month: {{ date('F', mktime(0,0,0,$month)) }} @endif</p>
        <p>Generated: {{ $generatedAt }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Municipality</th>
                <th>Barangay</th>
                <th>Farmer Name</th>
                <th>Gender</th>
                <th>Farm Location</th>
                <th>Seedlings Received</th>
                <th>Date Received</th>
                <th>Variety</th>
                <th>Seedlings Planted</th>
                <th>Date Planted</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @php $totalReceived = 0; $totalPlanted = 0; @endphp
            @forelse($records as $i => $record)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $record->municipality }}</td>
                <td>{{ $record->barangay }}</td>
                <td>{{ $record->full_name }}</td>
                <td>{{ $record->is_male ? 'M' : ($record->is_female ? 'F' : '') }}</td>
                <td>{{ $record->farm_barangay }}, {{ $record->farm_municipality }}</td>
                <td>{{ $record->seedlings_received }}</td>
                <td>{{ $record->date_received?->format('m/d/Y') ?? '' }}</td>
                <td>{{ $record->variety }}</td>
                <td>{{ $record->seedlings_planted }}</td>
                <td>{{ $record->date_planted?->format('m/d/Y') ?? '' }}</td>
                <td>{{ $record->remarks }}</td>
            </tr>
            @php
                $totalReceived += is_numeric($record->seedlings_received) ? $record->seedlings_received : 0;
                $totalPlanted += $record->seedlings_planted;
            @endphp
            @empty
            <tr><td colspan="12" style="text-align:center;">No records found.</td></tr>
            @endforelse
            @if($records->count() > 0)
            <tr class="total-row">
                <td colspan="6" style="text-align:right;">TOTAL:</td>
                <td>{{ $totalReceived }}</td>
                <td></td>
                <td></td>
                <td>{{ $totalPlanted }}</td>
                <td></td>
                <td></td>
            </tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        <table style="width:100%; margin-top:40px; border:none;">
            <tr>
                <td style="width:33%; text-align:center; border:none; padding-top:30px;">
                    <hr style="width:80%;">
                    <strong>Prepared by</strong>
                </td>
                <td style="width:33%; text-align:center; border:none; padding-top:30px;">
                    <hr style="width:80%;">
                    <strong>Reviewed by</strong>
                </td>
                <td style="width:33%; text-align:center; border:none; padding-top:30px;">
                    <hr style="width:80%;">
                    <strong>Noted by</strong>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
