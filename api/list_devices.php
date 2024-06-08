<?php
require_once("../db_connect.php");
require_once("../log_function.php"); 
header('Content-Type: application/json');

$dblink = db_connect(); 

// query to select device_id, device_type, and status from devices
$sql = "SELECT device_id, device_type, status FROM devices";
$result = $dblink->query($sql);

$devices = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        // add devices to devices[] table
        $devices[] = [
            'device_id' => $row['device_id'],
            'device_type' => $row['device_type'],
            'status' => $row['status']  
        ];
    }
    // put the devices as the data in the response
    $response = [
        'Status' => 'SUCCESS',
        'Data' => $devices,
        'Action' => 'none'
    ];
    echo json_encode($response);
    log_action('List Devices', 'Successfully retrieved all active devices');
} else {
    // if the query fails
    $response = [
        'Status' => 'ERROR',
        'MSG' => 'Failed to retrieve devices.',
        'Action' => 'Database_query_failure'
    ];
    echo json_encode($response);
    log_action('List Devices Error', 'Failed to retrieve devices: ' . $dblink->error);
}
?>
