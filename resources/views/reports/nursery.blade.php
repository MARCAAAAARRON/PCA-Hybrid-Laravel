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

    <style>
        .signature-section { margin-top: 50px; page-break-inside: avoid; }
        .signature-table { width: 100%; border: none; }
        .signature-table td { width: 33%; text-align: center; border: none; vertical-align: bottom; visibility: visible; }
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
