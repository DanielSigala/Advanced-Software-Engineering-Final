<?php
require_once("../db_connect.php");
require_once("../log_function.php");

header('Content-Type: application/json');
$dblink = db_connect();

$response = [];

$serial_number = isset($_REQUEST['serial_number']) ? $_REQUEST['serial_number'] : '';
$new_device_type = isset($_REQUEST['new_device_type']) ? $_REQUEST['new_device_type'] : null;
$new_manufacturer_name = isset($_REQUEST['new_manufacturer_name']) ? $_REQUEST['new_manufacturer_name'] : null;
$new_serial_number = isset($_REQUEST['new_serial_number']) ? $_REQUEST['new_serial_number'] : null;
$new_status = isset($_REQUEST['new_status']) ? $_REQUEST['new_status'] : null;

//check if serial number is proviced
if (empty($serial_number)) {
    $response = [
        'Status' => 'ERROR',
        'MSG' => 'Original serial number is required.',
        'Action' => 'Input_serial_number'
    ];
    echo json_encode($response);
    exit;
}
//check if serial number is too long
if (strlen($serial_number) > 67) {
    $response = [
        'Status' => 'ERROR',
        'MSG' => 'Original serial number exceeds 67 characters.',
        'Action' => 'Verify_serial_number_length'
    ];
    echo json_encode($response);
    exit;
}

// check if the original serial number exists
$checkSerialSql = "SELECT serial_number FROM serials WHERE serial_number = '".mysqli_real_escape_string($dblink, $serial_number)."'";
$serialResult = $dblink->query($checkSerialSql);
if ($serialResult->num_rows == 0) {
    $response = [
        'Status' => 'ERROR',
        'MSG' => 'Original serial number does not exist.',
        'Action' => 'Verify_serial_number_existence'
    ];
    echo json_encode($response);
    exit;
}

// check if new serial number if provided and if its too long
if ($new_serial_number !== null && strlen($new_serial_number) > 67) {
    $response = [
        'Status' => 'ERROR',
        'MSG' => 'New serial number exceeds 67 characters.',
        'Action' => 'Verify_new_serial_number_length'
    ];
    echo json_encode($response);
    exit;
}

//check if new serial number already exists in db
$checkNewSerialSql = "SELECT serial_number FROM serials WHERE serial_number = '".mysqli_real_escape_string($dblink, $new_serial_number)."'";
$newSerialResult = $dblink->query($checkNewSerialSql);
if ($newSerialResult->num_rows > 0) {
        $response = [
            'Status' => 'ERROR',
            'MSG' => 'New serial number already exists.',
            'Action' => 'Verify_new_serial_number_existence'
        ];
        echo json_encode($response);
        exit;
    }

//check if new device type is provided and if its too long, if it exists, or inactive
if ($new_device_type !== null) {
    if (strlen($new_device_type) > 64) {
        $response = [
            'Status' => 'ERROR',
            'MSG' => 'New device type exceeds 64 characters.',
            'Action' => 'Verify_new_device_type_length'
        ];
        echo json_encode($response);
        exit;
    }
    $deviceTypeEscaped = mysqli_real_escape_string($dblink, $new_device_type);
    $deviceQuery = "SELECT device_id, status FROM devices WHERE device_type = '$deviceTypeEscaped'";
    $deviceResult = $dblink->query($deviceQuery);
    if ($deviceResult->num_rows == 0) {
        $response = [
            'Status' => 'ERROR',
            'MSG' => "Device type '$new_device_type' does not exist.",
            'Action' => 'Validate_new_device'
        ];
        echo json_encode($response);
        exit;
    }
    $device = $deviceResult->fetch_assoc();
    if ($device['status'] !== 'active') {
        $response = [
            'Status' => 'ERROR',
            'MSG' => "New device '$new_device_type' is inactive.",
            'Action' => 'Check_new_device_active'
        ];
        echo json_encode($response);
        exit;
    }
}

// check if new manufacturer is provided, if its too long, if it exists, and if its inactive
if ($new_manufacturer_name !== null) {
    if (strlen($new_manufacturer_name) > 64) {
        $response = [
            'Status' => 'ERROR',
            'MSG' => 'New manufacturer name exceeds 64 characters.',
            'Action' => 'Verify_new_manufacturer_name_length'
        ];
        echo json_encode($response);
        exit;
    }
    $manufacturerNameEscaped = mysqli_real_escape_string($dblink, $new_manufacturer_name);
    $manufacturerQuery = "SELECT manufacturer_id, status FROM manufacturers WHERE manufacturer_name = '$manufacturerNameEscaped'";
    $manufacturerResult = $dblink->query($manufacturerQuery);
    if ($manufacturerResult->num_rows == 0) {
        $response = [
            'Status' => 'ERROR',
            'MSG' => "Manufacturer name '$new_manufacturer_name' does not exist.",
            'Action' => 'Validate_new_manufacturer'
        ];
        echo json_encode($response);
        exit;
    }
    $manufacturer = $manufacturerResult->fetch_assoc();
    if ($manufacturer['status'] !== 'active') {
        $response = [
            'Status' => 'ERROR',
            'MSG' => "New manufacturer '$new_manufacturer_name' is inactive.",
            'Action' => 'Check_new_manufacturer_active'
        ];
        echo json_encode($response);
        exit;
    }
}

// check new status
if ($new_status !== null && !in_array($new_status, ['active', 'inactive'], true)) {
    $response = [
        'Status' => 'ERROR',
        'MSG' => "Invalid status '$new_status' provided.",
        'Action' => 'Validate_new_status'
    ];
    echo json_encode($response);
    exit;
}

// update based on what fields were provided
$updateParts = [];
$changedFields = [];
if ($new_device_type) {
    $updateParts[] = "device_id = '{$device['device_id']}'";
	$changedFields[] = "device type";
}
if ($new_manufacturer_name) {
    $updateParts[] = "manufacturer_id = '{$manufacturer['manufacturer_id']}'";
    $changedFields[] = "manufacturer name";
}
if ($new_serial_number) {
    $updateParts[] = "serial_number = '$new_serial_number'";
    $changedFields[] = "serial number";
}
if ($new_status) {
    $updateParts[] = "status = '$new_status'";
    $changedFields[] = "status";
}
if (!empty($updateParts)) {//execute query
    $updateSql = "UPDATE serials SET " . implode(', ', $updateParts) . " WHERE serial_number = '$serial_number'";
    if ($dblink->query($updateSql) && $dblink->affected_rows > 0) {
        $response = [
            'Status' => 'SUCCESS',
            'MSG' => 'Equipment details modified successfully. Updated fields: ' . implode(', ', $changedFields) . '.',
            'Action' => 'none'
        ];
    } else {
        $response = [//if no changes
            'Status' => 'ERROR',
            'MSG' => 'No modifications were made: ' . $dblink->error,
            'Action' => 'Database_update_failure'
        ];
    }
} else {
    $response = [//if query failed
        'Status' => 'WARNING',
        'MSG' => 'No updates were requested.',
        'Action' => 'no_update_requested'
    ];
}

echo json_encode($response);
?>
