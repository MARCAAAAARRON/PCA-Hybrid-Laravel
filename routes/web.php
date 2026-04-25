<?php

use Illuminate\Support\Facades\Route;
use App\Models\FieldSite;

Route::get('/', function () {
    return view('welcome');
});

// ─── QR Code Routes ─────────────────────────────────────────────
// Quick-add: scanned QR redirects to Create Monthly Harvest with site pre-filled
Route::get('/site/{fieldSite}/quick-add', function (FieldSite $fieldSite) {
    return redirect()->to(
        '/admin/monthly-harvests/create?field_site_id=' . $fieldSite->id
    );
})->middleware(['auth'])->name('site.quick-add');

// Printable QR code page (for printing & sticking on field markers)
Route::get('/site/{fieldSite}/qr', function (FieldSite $fieldSite) {
    $quickAddUrl = url("/site/{$fieldSite->id}/quick-add");
    return view('qr-code-print', [
        'site' => $fieldSite,
        'qrUrl' => $quickAddUrl,
    ]);
})->middleware(['auth'])->name('site.qr');
