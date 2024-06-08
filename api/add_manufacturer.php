<?php
require_once("../db_connect.php");
require_once("../log_function.php");

header('Content-Type: application/json');
$dblink = db_connect();
$response = [];

//check if manufacturer is empty
if (empty($manufacturerName)) {
    $response = [
        'Status' => 'ERROR',
        'MSG' => 'Manufacturer name cannot be empty.',
        'Action' => 'validate_manufacturer_input'
    ];
    echo json_encode($response);
    exit;
}
//check if manufacturer exceeds length in db
if (strlen($manufacturerName) > 64) {
    $response = [
        'Status' => 'ERROR',
        'MSG' => 'Manufacturer name exceeds maximum length of 64 characters.',
        'Action' => 'validate_manufacturer_input'
    ];
    echo json_encode($response);
    exit;
}


$manufacturerName = $dblink->real_escape_string($manufacturerName);

// Check if manufacturer name already exists in db
$checkQuery = "SELECT * FROM manufacturers WHERE manufacturer_name = '$manufacturerName'";
$checkResult = $dblink->query($checkQuery);
if ($checkResult->num_rows > 0) {
    $response = [
        'Status' => 'ERROR',
        'MSG' => 'Manufacturer name already exists.',
        'Action' => 'check_existing'
    ];
    echo json_encode($response);
    log_action('Add Manufacturer Error', 'Attempted to add existing manufacturer name: ' . $manufacturerName);
    exit;
}

// Insert new manufacturer
$insertQuery = "INSERT INTO manufacturers (manufacturer_name) VALUES ('$manufacturerName')";
if ($dblink->query($insertQuery)) {
    $response = [
        'Status' => 'SUCCESS',
        'MSG' => 'New manufacturer added successfully.',
        'Action' => 'none'
    ];
    log_action('Add Manufacturer', 'New manufacturer added: ' . $manufacturerName);
} else {
    $response = [
        'Status' => 'ERROR',
        'MSG' => 'Failed to add manufacturer: ' . $dblink->error,
        'Action' => 'add_manufacturer'
    ];
    log_action('Add Manufacturer Error', 'Failed to add new manufacturer: ' . $manufacturerName . ' - ' . $dblink->error);
}

echo json_encode($response);
?>
