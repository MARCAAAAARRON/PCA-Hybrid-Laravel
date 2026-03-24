<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Monthly Harvest Report</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 8px; margin: 0; padding: 15px; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #2d6a2e; padding-bottom: 10px; }
        .header h1 { font-size: 14px; color: #2d6a2e; margin: 0; }
        .header h2 { font-size: 11px; color: #333; margin: 3px 0; }
        .header p { font-size: 9px; color: #666; margin: 2px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #2d6a2e; color: white; padding: 3px 4px; text-align: center; font-size: 7px; }
        td { padding: 2px 4px; border-bottom: 1px solid #ddd; font-size: 7px; text-align: center; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .total-row td { font-weight: bold; background-color: #e8f5e9; border-top: 2px solid #2d6a2e; }
    </style>
</head>
<body>
    <div class="header">
        <h1>PHILIPPINE COCONUT AUTHORITY</h1>
        <h2>On-Farm Hybrid Seednut Production Report</h2>
        <p>{{ $fieldSite }} | Year: {{ $year }}</p>
        <p>Generated: {{ $generatedAt }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Location</th>
                <th>Partner/Farm</th>
                <th>Area (Ha)</th>
                <th>Age</th>
                <th>Palms</th>
                <th>Variety</th>
                <th>Jan</th><th>Feb</th><th>Mar</th><th>Apr</th><th>May</th><th>Jun</th>
                <th>Jul</th><th>Aug</th><th>Sep</th><th>Oct</th><th>Nov</th><th>Dec</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $i => $record)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td style="text-align:left;">{{ $record->location }}</td>
                <td style="text-align:left;">{{ $record->farm_name }}</td>
                <td>{{ $record->area_ha }}</td>
                <td>{{ $record->age_of_palms }}</td>
                <td>{{ $record->num_hybridized_palms }}</td>
                <td>{{ $record->variety }}</td>
                <td>{{ $record->production_jan }}</td>
                <td>{{ $record->production_feb }}</td>
                <td>{{ $record->production_mar }}</td>
                <td>{{ $record->production_apr }}</td>
                <td>{{ $record->production_may }}</td>
                <td>{{ $record->production_jun }}</td>
                <td>{{ $record->production_jul }}</td>
                <td>{{ $record->production_aug }}</td>
                <td>{{ $record->production_sep }}</td>
                <td>{{ $record->production_oct }}</td>
                <td>{{ $record->production_nov }}</td>
                <td>{{ $record->production_dec }}</td>
                <td><strong>{{ $record->total_production }}</strong></td>
            </tr>
            @empty
            <tr><td colspan="20" style="text-align:center;">No records found.</td></tr>
            @endforelse
        </tbody>
    </table>

    <table style="width:100%; margin-top:40px; border:none;">
        <tr>
            <td style="width:33%; text-align:center; border:none; padding-top:30px;">
                <hr style="width:80%;"><strong>Prepared by</strong>
            </td>
            <td style="width:33%; text-align:center; border:none; padding-top:30px;">
                <hr style="width:80%;"><strong>Reviewed by</strong>
            </td>
            <td style="width:33%; text-align:center; border:none; padding-top:30px;">
                <hr style="width:80%;"><strong>Noted by</strong>
            </td>
        </tr>
    </table>
</body>
</html>
