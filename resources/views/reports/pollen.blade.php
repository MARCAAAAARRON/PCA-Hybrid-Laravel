<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pollen Production Report</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9px; margin: 0; padding: 15px; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #2d6a2e; padding-bottom: 10px; }
        .header h1 { font-size: 14px; color: #2d6a2e; margin: 0; }
        .header h2 { font-size: 12px; color: #333; margin: 3px 0; }
        .header p { font-size: 9px; color: #666; margin: 2px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #2d6a2e; color: white; padding: 4px 6px; text-align: center; font-size: 8px; }
        td { padding: 3px 6px; border-bottom: 1px solid #ddd; font-size: 8px; text-align: center; }
        tr:nth-child(even) { background-color: #f9f9f9; }
    </style>
</head>
<body>
    <div class="header">
        <h1>PHILIPPINE COCONUT AUTHORITY</h1>
        <h2>Pollen Production and Inventory Report</h2>
        <p>{{ $fieldSite }} | Year: {{ $year }} @if($month) | Month: {{ date('F', mktime(0,0,0,$month)) }} @endif</p>
        <p>Generated: {{ $generatedAt }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Month</th>
                <th>Variety</th>
                <th>Prev Balance</th>
                <th>Source</th>
                <th>Received</th>
                <th>Wk1</th><th>Wk2</th><th>Wk3</th><th>Wk4</th><th>Wk5</th>
                <th>Total Used</th>
                <th>End Balance</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $i => $record)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $record->month_label }}</td>
                <td style="text-align:left;">{{ $record->pollen_variety }}</td>
                <td>{{ $record->ending_balance_prev }}</td>
                <td style="text-align:left;">{{ $record->pollen_source }}</td>
                <td>{{ $record->pollens_received }}</td>
                <td>{{ $record->week1 }}</td>
                <td>{{ $record->week2 }}</td>
                <td>{{ $record->week3 }}</td>
                <td>{{ $record->week4 }}</td>
                <td>{{ $record->week5 }}</td>
                <td><strong>{{ $record->total_utilization }}</strong></td>
                <td><strong>{{ $record->ending_balance }}</strong></td>
                <td style="text-align:left;">{{ $record->remarks }}</td>
            </tr>
            @empty
            <tr><td colspan="14" style="text-align:center;">No records found.</td></tr>
            @endforelse
        </tbody>
    </table>

    <style>
        .signature-section { margin-top: 50px; page-break-inside: avoid; }
        .signature-table { width: 100%; border: none; }
        .signature-table td { width: 33%; text-align: center; border: none; vertical-align: bottom; }
        .signature-box { position: relative; height: 60px; display: flex; flex-direction: column; align-items: center; justify-content: flex-end; }
        .signature-img { height: 40px; margin-bottom: -10px; }
        .signature-line { width: 80%; border: none; border-bottom: 1px solid #000; margin: 0 auto; }
        .signature-label { font-size: 8px; font-weight: bold; margin-top: 4px; display: block; }
        .signatory-name { font-size: 9px; font-weight: bold; text-transform: uppercase; }
        .signatory-role { font-size: 7px; color: #444; }
    </style>

    <div class="signature-section">
        <table class="signature-table">
            <tr>
                <td>
                    <div class="signature-box">
                        @if($signatories['prepared']?->signature_image)
                            <img src="{{ Storage::disk('cloudinary')->url($signatories['prepared']->signature_image) }}" class="signature-img">
                        @endif
                        <div class="signatory-name">{{ $signatories['prepared']?->name ?? '________________' }}</div>
                        <div class="signature-line"></div>
                        <span class="signature-label">Prepared by</span>
                        <div class="signatory-role">{{ $signatories['prepared']?->role_title ?? 'Project Staff' }}</div>
                    </div>
                </td>
                <td>
                    <div class="signature-box">
                        @if($signatories['reviewed']?->signature_image)
                            <img src="{{ Storage::disk('cloudinary')->url($signatories['reviewed']->signature_image) }}" class="signature-img">
                        @endif
                        <div class="signatory-name">{{ $signatories['reviewed']?->name ?? '________________' }}</div>
                        <div class="signature-line"></div>
                        <span class="signature-label">Reviewed by</span>
                        <div class="signatory-role">{{ $signatories['reviewed']?->role_title ?? 'Technical Staff' }}</div>
                    </div>
                </td>
                <td>
                    <div class="signature-box">
                        @if($signatories['noted']?->signature_image)
                            <img src="{{ Storage::disk('cloudinary')->url($signatories['noted']->signature_image) }}" class="signature-img">
                        @endif
                        <div class="signatory-name">{{ $signatories['noted']?->name ?? '________________' }}</div>
                        <div class="signature-line"></div>
                        <span class="signature-label">Noted by</span>
                        <div class="signatory-role">{{ $signatories['noted']?->role_title ?? 'Division Chief' }}</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
