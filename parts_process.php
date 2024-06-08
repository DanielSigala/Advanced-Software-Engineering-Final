<?php
include("/home/ubuntu/db_connect.php");
ini_set('max_execution_time', 0);
$filePath = $argv[1];
$partIndex = (int) $argv[2];
$linesPerFile = 10000;
$errorLogFilePath = "/var/www/html/logs/er_log.txt";
$performanceLogFilePath = "/var/www/html/logs/pr_log.txt";

$dblink = db_connect();
$records = [];
$lineNumber = 0;
$successfulRecords = 0;
$errorsEncountered = 0;
$batchSize = 100; // Adjust based on your system's optimal performance

$timeStart = microtime(true);

if (($fileHandle = fopen($filePath, "r")) !== FALSE) {
    while (($row = fgetcsv($fileHandle)) !== FALSE) {
        $lineNumber++;
        $globalLineNumber = $partIndex * $linesPerFile + $lineNumber;

        if (empty($row[0]) || empty($row[1]) || empty($row[2])) {
            logError("Missing data", $globalLineNumber);
            $errorsEncountered++;
            continue; // Skip this record
        }

        // Prepare data for batch insertion
        $records[] = "('{$dblink->real_escape_string($row[0])}', '{$dblink->real_escape_string($row[1])}', '{$dblink->real_escape_string($row[2])}')";

        if (count($records) >= $batchSize) {
            // Insert the current batch and handle exceptions
            $inserted = insertBatch($dblink, $records, $globalLineNumber - count($records) + 1);
            $successfulRecords += $inserted;
            $records = []; // Clear records for the next batch
        }
    }
    // Insert any remaining records
    if (!empty($records)) {
        $inserted = insertBatch($dblink, $records, $globalLineNumber - count($records) + 1);
        $successfulRecords += $inserted;
    }
    fclose($fileHandle);
}
$dblink->close();

$timeEnd = microtime(true);
$executionTime = $timeEnd - $timeStart;
$rowsPerSecond = $lineNumber > 0 ? $successfulRecords / $executionTime : 0;

// Log performance metrics
$processSummary = "Part: $partIndex, Execution time: $executionTime seconds, Rows per second: $rowsPerSecond, Total lines processed: $lineNumber, Successful records: $successfulRecords, Errors encountered: $errorsEncountered\n";
file_put_contents($performanceLogFilePath, $processSummary, FILE_APPEND);

echo $processSummary;

function logError($message, $globalLineNumber) {
    global $errorLogFilePath;
    $errorMsg = "Error at global line $globalLineNumber: $message\n";
    file_put_contents($errorLogFilePath, $errorMsg, FILE_APPEND);
}

function insertBatch($dblink, $records, $startLine) {
    global $errorsEncountered, $errorLogFilePath;
    $query = "INSERT INTO Devices (device_type, manufacturer, serial_number) VALUES " . join(',', $records) . ";";
    $dblink->begin_transaction();
    try {
        if (!$dblink->query($query)) {
            throw new Exception($dblink->error);
        }
        $dblink->commit();
        return count($records);
    } catch (Exception $e) {
        $dblink->rollback();
        $errorsEncountered += count($records);
        logError($e->getMessage(), $startLine);
        return 0;
    }
}

?>
