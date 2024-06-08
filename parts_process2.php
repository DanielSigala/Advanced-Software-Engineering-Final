<?php
include("/home/ubuntu/db_connect.php");
ini_set('max_execution_time', 0);
$filePath = $argv[1];
$partIndex = (int) $argv[2];
$linesPerFile = 10000;
$errorLogFilePath = "/var/www/html/logs/e_log.txt";
$performanceLogFilePath = "/var/www/html/logs/p_log.txt";
$dblink = db_connect();

$lineNumber = 0;
$successfulRecords = 0;
$errorsEncountered = 0;

$timeStart = microtime(true);

// Open the file correctly using fopen
if (($fileHandle = fopen($filePath, "r")) !== FALSE) {
    while (($row = fgetcsv($fileHandle)) !== FALSE) {
        $lineNumber++;
        // Correctly calculate the global line number considering all parts
        $globalLineNumber = ($partIndex * $linesPerFile) + $lineNumber;

        // Data validation
        if (empty($row[0]) || empty($row[1]) || empty($row[2])) {
            $missingFields = [];
            if (empty($row[0])) $missingFields[] = "device_type";
            if (empty($row[1])) $missingFields[] = "manufacturer";
            if (empty($row[2])) $missingFields[] = "serial_number";
            $errorMessage = "Missing data at global line $globalLineNumber for fields: " . implode(", ", $missingFields) . PHP_EOL;
            file_put_contents($errorLogFilePath, $errorMessage, FILE_APPEND | LOCK_EX); // Ensure atomic writes
            $errorsEncountered++;
            continue;
        }

        $query = "INSERT INTO Devices (device_type, manufacturer, serial_number) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE serial_number=serial_number;";
        if ($stmt = $dblink->prepare($query)) {
            $stmt->bind_param("sss", $row[0], $row[1], $row[2]);
            if (!$stmt->execute()) {
                $errorsEncountered++;
                $errorMessage = "Error at global line $globalLineNumber: " . $stmt->error . PHP_EOL;
                file_put_contents($errorLogFilePath, $errorMessage, FILE_APPEND | LOCK_EX);
            } else {
                $successfulRecords++;
            }
        }
    }
    fclose($fileHandle); // Close the file handle properly
}
$dblink->close();

$timeEnd = microtime(true);
$seconds = $timeEnd - $timeStart;
$executionTime = $seconds / 60;
$rowsPerSecond = $successfulRecords / $seconds; // Calculate rows per second

// Compile and log performance metrics accurately
$processSummary = "Part $partNumber processed in $executionTime minutes ($seconds seconds). Rows per second: $rowsPerSecond. Total lines processed: $lineNumber. Successful records: $successfulRecords. Errors encountered: $errorsEncountered." . PHP_EOL;
file_put_contents($performanceLogFilePath, $processSummary, FILE_APPEND | LOCK_EX);

echo $processSummary;
?>
