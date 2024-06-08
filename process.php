<?php
include("/home/ubuntu/db_connect.php");
ini_set('max_execution_time', 0);

// Configuration
$filePath = $argv[1];
$partIndex = (int) $argv[2];
$linesPerFile = 1000;
$errorLogFilePath = "error_log.txt";
$performanceLogFilePath = "performance_log.txt";

// Connect to DB
$dblink = db_connect();

// Initialize counters
$lineNumber = 0;
$successfulRecords = 0;
$errorsEncountered = 0;

$timeStart = microtime(true);

if (($fileHandle = fopen($filePath, "r")) !== FALSE) {
    while (($row = fgetcsv($fileHandle)) !== FALSE) {
        $lineNumber++;
        $globalLineNumber = ($partIndex * $linesPerFile) + $lineNumber;

        // Check for missing fields
        if (empty($row[0]) || empty($row[1]) || empty($row[2])) {
            logError("Missing data", $globalLineNumber);
            $errorsEncountered++;
            continue;
        }

        // Check for unique serial number
        if (!isSerialUnique($dblink, $row[2])) {
            logError("Duplicate serial number", $globalLineNumber);
            $errorsEncountered++;
            continue;
        }

        // Insert record
        $query = "INSERT INTO Devices (device_type, manufacturer, serial_number) VALUES (?, ?, ?)";
        if ($stmt = $dblink->prepare($query)) {
            $stmt->bind_param("sss", $row[0], $row[1], $row[2]);
            if ($stmt->execute()) {
                $successfulRecords++;
            } else {
                logError("DB error: " . $stmt->error, $globalLineNumber);
                $errorsEncountered++;
            }
        }
    }
    fclose($fileHandle);
}
$dblink->close();

// Calculate performance metrics
$timeEnd = microtime(true);
$executionTime = $timeEnd - $timeStart;
$rowsPerSecond = $successfulRecords / $executionTime;

// Log performance metrics
$processSummary = sprintf("Part: %d, Execution time: %.2f seconds, Rows per second: %.2f, Total lines processed: %d, Successful records: %d, Errors encountered: %d\n", $partIndex, $executionTime, $rowsPerSecond, $lineNumber, $successfulRecords, $errorsEncountered);
file_put_contents($performanceLogFilePath, $processSummary, FILE_APPEND);

echo $processSummary;

function logError($message, $globalLineNumber) {
    global $errorLogFilePath;
    $errorMsg = "Error at global line $globalLineNumber: $message\n";
    file_put_contents($errorLogFilePath, $errorMsg, FILE_APPEND);
}

function isSerialUnique($dblink, $serialNumber) {
    $query = "SELECT COUNT(*) FROM Devices WHERE serial_number = ?";
    if ($stmt = $dblink->prepare($query)) {
        $stmt->bind_param("s", $serialNumber);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        return $count == 0; // Return true if serial is unique
    }
    return false; // Default to false in case of error
}

function sanitizeField(&$field, $fieldType, $globalLineNumber) {
    global $errorLogFilePath;
    $originalField = $field;

    // Remove disallowed characters like apostrophes or commas
    $field = str_replace(["'", ",", "/""], "", $field);

    if ($field !== $originalField) {
        $message = "Error fixed on line $globalLineNumber field $fieldType. Extra characters removed. Record was still inserted.\n";
        file_put_contents($errorLogFilePath, $message, FILE_APPEND);
    }
}
?>