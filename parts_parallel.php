<?php
include("db_connect.php");
ini_set('max_execution_time', 0);
$partsDirectory = '/home/daniel/parts';
$files = array_diff(scandir($partsDirectory), array('..', '.'));
natsort($files); // Natural sort to maintain order

foreach ($files as $index => $file) {
    if (pathinfo($file, PATHINFO_EXTENSION) === 'csv') {
        $command = "php process.php '{$partsDirectory}/{$file}' $index &";
        shell_exec($command);
    }
}

echo "Main Process Done. Check the logs for details.\n";
?>