<?php
require_once("../db_connect.php");
require_once("../log_function.php");

header('Content-Type: application/json');
$dblink = db_connect();
$response = [];

$serial_number = isset($_REQUEST['serial_number']) ? $_REQUEST['serial_number'] : '';
$device_type = isset($_REQUEST['device_type']) ? $_REQUEST['device_type'] : '';
$manufacturer_name = isset($_REQUEST['manufacturer_name']) ? $_REQUEST['manufacturer_name'] : '';

// cgeck if serial number is empty
if (empty($serial_number)) {
    $response = [
        'Status' => 'ERROR',
        'MSG' => 'Serial number is required.',
        'Action' => 'Input_serial_number'
    ];
    echo json_encode($response);
    log_action('Add Equipment API', 'Failed addition attempt due to missing serial number.');
    exit;
}
// check if device is empty
if (empty($device_type)) {
    $response = [
        'Status' => 'ERROR',
        'MSG' => 'Device Type is required.',
        'Action' => 'Input_device_type'
    ];
    echo json_encode($response);
    log_action('Add Equipment API', 'Failed addition attempt due to missing device type.');
    exit;
}

// check if manufacturer is empty
if (empty($manufacturer_name)) {
    $response = [
        'Status' => 'ERROR',
        'MSG' => 'Manufacturer name is required.',
        'Action' => 'Input_manufacturer_name'
    ];
    echo json_encode($response);
    log_action('Add Equipment API', 'Failed addition attempt due to missing manufacturer name.');
    exit;
}

// get the device details from db
$deviceEscaped = mysqli_real_escape_string($dblink, $device_type);
$deviceQuery = "SELECT device_id, status FROM devices WHERE device_type = '$deviceEscaped'";
$deviceResult = $dblink->query($deviceQuery);

// if no results for device
if ($deviceResult->num_rows == 0) {
    $response = [
        'Status' => 'ERROR',
        'MSG' => "Device type '$device_type' does not exist.",
        'Action' => 'Validate_device_existence'
    ];
    echo json_encode($response);
    log_action('Add Equipment API', "Device type does not exist: $device_type");
    exit;
}
// if the device exists but is inactive
$device = $deviceResult->fetch_assoc();
if ($device['status'] !== 'active') {
    $response = [
        'Status' => 'ERROR',
        'MSG' => "Device type '$device_type' is inactive.",
        'Action' => 'Check_device_active'
    ];
    echo json_encode($response);
    log_action('Add Equipment API', "Device type is inactive: $device_type");
    exit;
}

// get manufacturer details from db
$manufacturerEscaped = mysqli_real_escape_string($dblink, $manufacturer_name);
$manufacturerQuery = "SELECT manufacturer_id, status FROM manufacturers WHERE manufacturer_name = '$manufacturerEscaped'";
$manufacturerResult = $dblink->query($manufacturerQuery);

if ($manufacturerResult->num_rows == 0) {
    $response = [
        'Status' => 'ERROR',
        'MSG' => "Manufacturer '$manufacturer_name' does not exist.",
        'Action' => 'Validate_manufacturer_existence'
    ];
    echo json_encode($response);
    log_action('Add Equipment API', "Manufacturer does not exist: $manufacturer_name");
    exit;
}

// if it exists but is inactive
$manufacturer = $manufacturerResult->fetch_assoc();
if ($manufacturer['status'] !== 'active') {
    $response = [
        'Status' => 'ERROR',
        'MSG' => "Manufacturer '$manufacturer_name' is inactive.",
        'Action' => 'Check_manufacturer_active'
    ];
    echo json_encode($response);
    log_action('Add Equipment API', "Manufacturer is inactive: $manufacturer_name");
    exit;
}

// Insert new equipment
$insertQuery = "INSERT INTO serials (device_id, manufacturer_id, serial_number) VALUES ('{$device['device_id']}', '{$manufacturer['manufacturer_id']}', '$serial_number')";
if ($dblink->query($insertQuery)) {
    $response = [
        'Status' => 'SUCCESS',
        'MSG' => 'New equipment added successfully.',
        'Action' => 'none'
    ];
    log_action('Add Equipment API', "New equipment added successfully with serial number: $serial_number");
} else {
	//if failed to insert into db
    $response = [
        'Status' => 'ERROR',
        'MSG' => 'Failed to add equipment: ' . $dblink->error,
        'Action' => 'add_equipment_failure'
    ];
    log_action('Add Equipment API Error', "Failed to add new equipment: " . $dblink->error);
}

echo json_encode($response);
?>
