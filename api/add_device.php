<?php
require_once("../db_connect.php");
require_once("../log_function.php");

header('Content-Type: application/json');
$dblink = db_connect();
$response = [];

// Check if deviceType is empty
if (empty($deviceType)) {
    $response = [
        'Status' => 'ERROR',
        'MSG' => 'Device type cannot be empty.',
        'Action' => 'validate_device_input'
    ];
    echo json_encode($response);
    exit;
}
//check if deviceType is too long
if (strlen($deviceType) > 64) {
    $response = [
        'Status' => 'ERROR',
        'MSG' => 'Device type exceeds maximum length of 64 characters.',
        'Action' => 'validate_device_input'
    ];
    echo json_encode($response);
    exit;
}


$deviceType = $dblink->real_escape_string($deviceType);

// check if device type already exists
$checkQuery = "SELECT * FROM devices WHERE device_type = '$deviceType'";
$checkResult = $dblink->query($checkQuery);
if ($checkResult->num_rows > 0) {
    $response = [
        'Status' => 'ERROR',
        'MSG' => 'Device type already exists.',
        'Action' => 'check_existing_device'
    ];
    echo json_encode($response);
    log_action('Add Device Error', 'Attempted to add existing device type: ' . $deviceType);
    exit;
}

// Insert the device if checks are passed
$insertQuery = "INSERT INTO devices (device_type) VALUES ('$deviceType')";
if ($dblink->query($insertQuery)) {
    $response = [
        'Status' => 'SUCCESS',
        'MSG' => 'New device type added successfully.',
        'Action' => 'none'
    ];
    log_action('Add Device', 'New device type added: ' . $deviceType);
} else {
    $response = [
        'Status' => 'ERROR',
        'MSG' => 'Failed to add device type: ' . $dblink->error,
        'Action' => 'add_device'
    ];
    log_action('Add Device Error', 'Failed to add new device type: ' . $deviceType . ' - ' . $dblink->error);
}

echo json_encode($response);
?>

