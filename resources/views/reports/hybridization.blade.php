<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Hybridization Records Report</title>
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
        .status-badge { padding: 2px 6px; border-radius: 3px; color: white; font-size: 7px; }
        .status-draft { background-color: #9e9e9e; }
        .status-submitted { background-color: #ff9800; }
        .status-validated { background-color: #4caf50; }
        .status-revision { background-color: #f44336; }
    </style>
</head>
<body>
    <div class="header">
        <h1>PHILIPPINE COCONUT AUTHORITY</h1>
        <h2>Hybridization Records Report</h2>
        <p>{{ $fieldSite }} | Year: {{ $year }}</p>
        <p>Generated: {{ $generatedAt }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Hybrid Code</th>
                <th>Crop Type</th>
                <th>Parent A</th>
                <th>Parent B</th>
                <th>Date Planted</th>
                <th>Growth Status</th>
                <th>Status</th>
                <th>Created By</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $i => $record)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td><strong>{{ $record->hybrid_code }}</strong></td>
                <td>{{ $record->crop_type }}</td>
                <td>{{ $record->parent_line_a }}</td>
                <td>{{ $record->parent_line_b }}</td>
                <td>{{ $record->date_planted?->format('m/d/Y') ?? '' }}</td>
                <td>{{ ucfirst($record->growth_status) }}</td>
                <td><span class="status-badge status-{{ $record->status }}">{{ ucfirst($record->status) }}</span></td>
                <td>{{ $record->creator?->name ?? '' }}</td>
                <td>{{ \Illuminate\Support\Str::limit($record->notes, 50) }}</td>
            </tr>
            @empty
            <tr><td colspan="10" style="text-align:center;">No records found.</td></tr>
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
