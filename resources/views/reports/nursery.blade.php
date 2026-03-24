<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Nursery Operations Report</title>
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
        .batch-header { background-color: #e8f5e9; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>PHILIPPINE COCONUT AUTHORITY</h1>
        <h2>Nursery Operations Report</h2>
        <p>{{ $fieldSite }} | Year: {{ $year }} @if($month) | Month: {{ date('F', mktime(0,0,0,$month)) }} @endif</p>
        <p>Generated: {{ $generatedAt }}</p>
    </div>

    @forelse($records as $operation)
    <table style="margin-bottom: 15px;">
        <tr class="batch-header">
            <td colspan="8">
                {{ $operation->proponent_entity }} — {{ $operation->barangay_municipality }}
                ({{ $operation->report_type === 'terminal' ? 'Terminal Report' : 'Monthly Report' }})
                | Target: {{ number_format($operation->target_seednuts) }} seednuts
            </td>
        </tr>
        <tr>
            <th>Variety</th>
            <th>Harvested</th>
            <th>Sown</th>
            <th>Germinated</th>
            <th>Ungerminated</th>
            <th>Good @1ft</th>
            <th>Ready</th>
            <th>Dispatched</th>
        </tr>
        @forelse($operation->batches as $batch)
        <tr>
            <td>{{ $batch->variety }}</td>
            <td>{{ $batch->seednuts_harvested }}</td>
            <td>{{ $batch->seednuts_sown }}</td>
            <td>{{ $batch->seedlings_germinated }}</td>
            <td>{{ $batch->ungerminated_seednuts }}</td>
            <td>{{ $batch->good_seedlings }}</td>
            <td>{{ $batch->ready_to_plant }}</td>
            <td>{{ $batch->seedlings_dispatched }}</td>
        </tr>
        @empty
        <tr><td colspan="8" style="text-align:center;">No batches recorded.</td></tr>
        @endforelse
    </table>
    @empty
    <p style="text-align:center;">No nursery operations found.</p>
    @endforelse

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
