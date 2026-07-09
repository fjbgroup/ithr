<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$companyCode = 'FJB';
$labels = [$companyCode];
foreach (App\Models\Company::where('code', $companyCode)->pluck('name') as $name) {
    $labels[] = trim($name);
    $labels[] = trim(preg_replace('/^\d+\s+/', '', $name));
    
    // Also try stripping non-breaking spaces
    $labels[] = trim(preg_replace('/^\d+\s+/u', '', str_replace("\xC2\xA0", ' ', $name)));
}

echo "LABELS: \n";
echo json_encode($labels, JSON_PRETTY_PRINT);
echo "\n\nSTAFF RECORDS MATCHING EACH LABEL:\n";

foreach ($labels as $lbl) {
    $cnt = App\Models\Staff::where('company', $lbl)->count();
    $cnt_like = App\Models\Staff::where('company', 'LIKE', "%$lbl%")->count();
    echo "$lbl : Exact=$cnt, Like=$cnt_like\n";
}

$all = App\Models\Staff::select('company', \Illuminate\Support\Facades\DB::raw('count(*) as count'))->groupBy('company')->get();
echo "\nALL GROUPS IN DB:\n";
foreach ($all as $row) {
    echo "'{$row->company}' : {$row->count}\n";
}
