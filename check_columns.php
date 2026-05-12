<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$tables = DB::select('SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = ?', [DB::getDatabaseName()]);
echo str_pad('TABLE', 40) . ' | company_id | tenant_id | branch_id' . PHP_EOL;
echo str_repeat('-', 80) . PHP_EOL;
foreach ($tables as $t) {
    $name = $t->TABLE_NAME;
    $cid = Schema::hasColumn($name, 'company_id') ? ' YES ' : '     ';
    $tid = Schema::hasColumn($name, 'tenant_id') ? ' YES ' : '     ';
    $bid = Schema::hasColumn($name, 'branch_id') ? ' YES ' : '     ';
    if (trim($cid . $tid . $bid)) {
        echo str_pad($name, 40) . ' |  ' . $cid . '  |  ' . $tid . '  |  ' . $bid . PHP_EOL;
    }
}
