<?php
require_once("../db_connect.php");
require_once("../log_function.php");

header('Content-Type: application/json');
$dblink = db_connect();

$response = [];
$current_name = isset($_REQUEST['current_name']) ? $_REQUEST['current_name'] : null;
$new_name = isset($_REQUEST['new_name']) ? $_REQUEST['new_name'] : null;
$new_status = isset($_REQUEST['new_status']) ? $_REQUEST['new_status'] : null;
//check if current name is empty
if (empty($current_name)){
	$response = [
		'Status' => 'ERROR', 
		'MSG' => 'Current name can not be empty.', 
		'Action' => 'validate_device_details'
	];
    echo json_encode($response);
    log_action('Modify Device API', 'Attempted to update device without current name');
    exit;
}

//checks if current name exceeds length 
if (!empty($current_name)) {
	if (strlen($current_name) > 64){
		$response = [
			'Status' => 'ERROR', 
			'MSG' => 'Current name can not exceed length 64', 
			'Action' => 'validate_device_name'
		];
		echo json_encode($response);
		log_action('Modify Device API', 'Current name exceeds length of 64.');
		exit;
	}
    
}

//checks if both the new status and new name are empty
if (empty($new_name) && empty($new_status)) {
    $response = [
		'Status' => 'ERROR', 
		'MSG' => 'Both new name and new status cannot be empty simultaneously.', 
		'Action' => 'validate_device_details'
	];
    echo json_encode($response);
    log_action('Modify Device API', 'Attempted to update device without new name or status');
    exit;
}


$changes = [];
$updateParts = [];

$new_name = $dblink->real_escape_string($new_name);
$new_status = $dblink->real_escape_string($new_status);

//if there is a new name, checks if this name already exists in db
if (!empty($new_name)) {
	//check if new name exceeds length
	if (strlen($new_name) > 64){
		$response = [
			'Status' => 'ERROR', 
			'MSG' => 'New name can not exceed length 64', 
			'Action' => 'check_existing_name'
		];
		echo json_encode($response);
		log_action('Modify Device API', 'New name exceeds length of 64.');
		exit;
	}
	
    $checkNameExistSql = "SELECT device_id FROM devices WHERE device_type = '$new_name'";
    $checkResult = $dblink->query($checkNameExistSql);
    if ($checkResult->num_rows > 0) {
        $response = [
			'Status' => 'ERROR',
			'MSG' => 'New device name already exists.',
			'Action' => 'check_existing_name'
		];
        echo json_encode($response);
        log_action('Modify Device API', "New device name already exists: $new_name");
        exit;
    }
	//if it doesn't exist, save the name change to change array and continue
    $updateParts[] = "device_type = '$new_name'";
    $changes[] = "name to '$new_name'";
}
// if status is provided check if it is anything other than active or inactive
if (!empty($new_status)) {
    if (!in_array($new_status, ['active', 'inactive'])) {
        $response = [
            'Status' => 'ERROR',
            'MSG' => 'Invalid status provided.',
            'Action' => 'validate_status'
        ];
        echo json_encode($response);
        log_action('Modify Device API', "Invalid status provided: $new_status");
        exit;
    }
	//if valid status, save the status to change array and continue
    $updateParts[] = "status = '$new_status'";
    $changes[] = "status to '$new_status'";
}

//check if device exists
$findDeviceSql = "SELECT device_id FROM devices WHERE device_type = '$current_name'";
$findResult = $dblink->query($findDeviceSql);
if ($findResult->num_rows == 0) {
    $response = [
		'Status' => 'ERROR', 
		'MSG' => 'Current device name not found.', 
		'Action' => 'find_current_name'
	];
    echo json_encode($response);
    log_action('Modify Device API', "No device found with name: $current_name");
    exit;
}

$row = $findResult->fetch_assoc();
$device_id = $row['device_id'];

//try to update device with sql UPDATE statement
if (!empty($updateParts)) {
    $updateSql = "UPDATE devices SET " . implode(', ', $updateParts) . " WHERE device_id = $device_id";
    if ($dblink->query($updateSql)) {
        $changesText = implode(" and ", $changes);
        $response = [
			'Status' => 'SUCCESS', 
			'MSG' => "Device details updated successfully: $changesText.", 
			'Action' => 'none'
		];
        log_action('Modify Device API', "Device details updated for $current_name: $changesText");
    } else {
        $response = [
			'Status' => 'ERROR', 
			'MSG' => 'Error updating device details: ' . $dblink->error, 
			'Action' => 'update_device_details'
		];
        log_action('Modify Device API Error', "Failed to update device details for $current_name - " . $dblink->error);
    }
} else {//if there are no changes
    $response = [
		'Status' => 'WARNING',
		'MSG' => 'No updates were requested.',
		'Action' => 'no_update_requested'
	];
    log_action('Modify Device API', "No updates were requested for device: $current_name");
}

echo json_encode($response);
?>
