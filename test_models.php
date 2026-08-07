<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$dept = \App\Models\Department::first();
echo "Dept: " . json_encode($dept) . "\n";
$comp = \App\Models\Company::first();
echo "Comp: " . json_encode($comp) . "\n";
