<?php

function splitCSV($source, $destFolder, $linesPerFile = 1000) {
    $sourceFile = fopen($source, 'r');
    if (!$sourceFile) {
        die("Failed to open source file.");
    }
    
    $fileNumber = 1;
    $lineCounter = 0;
    $currentFile = false;
    
    while (!feof($sourceFile)) {
        // Start a new file when needed
        if ($lineCounter % $linesPerFile === 0) {
            if ($currentFile) {
                fclose($currentFile);
            }
            $fileName = $destFolder . 'part_' . $fileNumber . '.csv';
            $currentFile = fopen($fileName, 'w');
            if (!$currentFile) {
                die("Failed to open file: $fileName");
            }
            $fileNumber++;
        }
        
        // Read and write the current line
        $line = fgetcsv($sourceFile);
        if ($line) {
            fputcsv($currentFile, $line);
        }
        
        $lineCounter++;
    }
    
    if ($currentFile) {
        fclose($currentFile);
    }
    
    fclose($sourceFile);
    echo "Splitting complete: " . ($fileNumber - 1) . " files created.\n";
}

// Paths
$source = '/home/ubuntu/ugb040.csv';
$destFolder = '/home/ubuntu/parts/'; // Ensure this directory exists

splitCSV($source, $destFolder);

?>