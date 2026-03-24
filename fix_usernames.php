<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

foreach (User::all() as $user) {
    if (!$user->username) {
        $user->username = explode('@', $user->email)[0] ?: $user->id;
        $user->save();
        echo "Updated username for {$user->name}: {$user->username}\n";
    }
}
echo "Done.\n";
