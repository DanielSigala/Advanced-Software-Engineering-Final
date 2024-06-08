<?php
include("/home/ubuntu/db_connect.php");

$filePath = '/home/ubuntu/parts/part_113.csv';
$partIndex = 112; // Index for part 1
$linesPerFile = 10000;

// Define log file paths
$errorLogFilePath = "/var/www/html/logs/part_113_errors.txt"; // Error log for part 1
$performanceLogFilePath = "/var/www/html/logs/part_113_performance.txt"; // Performance log for part 1

$fileHandle = fopen($filePath, "r");
$lineNumber = 0;
$successfulRecords = 0;
$errorsEncountered = 0;

$timeStart = microtime(true);

if ($fileHandle !== FALSE) {
    $dblink = db_connect();
    while (($row = fgetcsv($fileHandle)) !== FALSE) {
        $lineNumber++;
        $globalLineNumber = ($partIndex * $linesPerFile) + $lineNumber;

        // Data validation
        if (empty($row[0]) || empty($row[1]) || empty($row[2])) {
            $missingFields = [];
            if (empty($row[0])) $missingFields[] = "device_type";
            if (empty($row[1])) $missingFields[] = "manufacturer";
            if (empty($row[2])) $missingFields[] = "serial_number";
            $errorMessage = "Missing data at global line $globalLineNumber for fields: " . implode(", ", $missingFields) . PHP_EOL;
            file_put_contents($errorLogFilePath, $errorMessage, FILE_APPEND);
            $errorsEncountered++;
            continue;
        }

        $query = "INSERT INTO test (device_type, manufacturer, serial_number) VALUES (?, ?, ?)";
        $stmt = $dblink->prepare($query);

        try {
            if (!$stmt->bind_param("sss", $row[0], $row[1], $row[2])) {
                throw new Exception("Failed to bind parameters.");
            }
            if (!$stmt->execute()) {
                throw new Exception($stmt->error);
            }
            $successfulRecords++;
        } catch (Exception $e) {
            $errorsEncountered++;
            $errorMessage = "Error at global line $globalLineNumber: " . $e->getMessage() . PHP_EOL;
            file_put_contents($errorLogFilePath, $errorMessage, FILE_APPEND);
        }
    }
    fclose($fileHandle);
    $dblink->close();
}

$timeEnd = microtime(true);
$seconds = $timeEnd - $timeStart;
$executionTime = $seconds / 60;
$rowsPerSecond = $successfulRecords / $seconds;

// Compile and log performance metrics
$processSummary = "Processed part_1.csv\nExecution time: $executionTime minutes ($seconds seconds)\nRows Per Second: $rowsPerSecond\nTotal Lines Processed: $lineNumber\nSuccessful Records: $successfulRecords\nErrors Encountered: $errorsEncountered\n";
file_put_contents($performanceLogFilePath, $processSummary);

echo $processSummary;
?>