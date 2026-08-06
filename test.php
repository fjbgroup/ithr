<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$item = App\Models\IT\InventoryItem::orderBy('id', 'desc')->first();
echo json_encode($item, JSON_PRETTY_PRINT);
