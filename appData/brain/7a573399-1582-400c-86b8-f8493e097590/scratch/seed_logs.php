<?php

use App\Models\AuditLog;
use App\Models\User;
use App\Models\MonthlyHarvest;
use App\Models\NurseryOperation;

$supervisor = User::where('role', 'supervisor')->first();
$manager = User::where('role', 'manager')->first();
$admin = User::where('role', 'admin')->first();

if (!$supervisor || !$manager || !$admin) {
    echo "Users not found.";
    exit;
}

$harvest = MonthlyHarvest::first();
$nursery = NurseryOperation::first();

$logs = [
    [
        'user_id' => $supervisor->id,
        'action' => 'create',
        'model_name' => MonthlyHarvest::class,
        'object_id' => $harvest?->id ?? 1,
        'details' => ['farm' => 'Balilihan', 'status' => 'Draft'],
        'ip_address' => '192.168.1.10',
        'created_at' => now()->subHours(2),
    ],
    [
        'user_id' => $supervisor->id,
        'action' => 'submit',
        'model_name' => MonthlyHarvest::class,
        'object_id' => $harvest?->id ?? 1,
        'details' => ['note' => 'Submitted for review'],
        'ip_address' => '192.168.1.10',
        'created_at' => now()->subHours(1),
    ],
    [
        'user_id' => $manager->id,
        'action' => 'validate',
        'model_name' => MonthlyHarvest::class,
        'object_id' => $harvest?->id ?? 1,
        'details' => ['note' => 'Checked and verified'],
        'ip_address' => '10.0.0.5',
        'created_at' => now()->subMinutes(45),
    ],
    [
        'user_id' => $supervisor->id,
        'action' => 'create',
        'model_name' => NurseryOperation::class,
        'object_id' => $nursery?->id ?? 1,
        'details' => ['batch' => 'BATCH-2026-001'],
        'ip_address' => '192.168.1.10',
        'created_at' => now()->subMinutes(30),
    ],
    [
        'user_id' => $admin->id,
        'action' => 'login',
        'model_name' => User::class,
        'object_id' => $admin->id,
        'details' => ['agent' => 'Chrome/Windows'],
        'ip_address' => '127.0.0.1',
        'created_at' => now()->subMinutes(10),
    ],
];

foreach ($logs as $log) {
    AuditLog::create($log);
}

echo "Audit logs seeded successfully.";
