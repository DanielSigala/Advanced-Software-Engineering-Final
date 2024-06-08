<?php
require_once("../db_connect.php");
require_once("../log_function.php");

header('Content-Type: application/json');
$dblink = db_connect();

$response = [];

// check if serial number is empty
if (empty($_GET['serial_number'])) {
    $response = [
        'Status' => 'ERROR',
        'MSG' => 'Serial number is required.',
        'Action' => 'Input_serial_number'
    ];
    echo json_encode($response);
    log_action('View Equipment API', 'Failed view attempt due to missing serial number.');
    exit;
}

$serial_number = $_GET['serial_number'];

// check if the serial number exceeds length
if (strlen($serial_number) > 68) {
    $response = [
        'Status' => 'ERROR',
        'MSG' => 'Serial number length exceeds 68 characters.',
        'Action' => 'Verify_serial_number'
    ];
    echo json_encode($response);
    log_action('View Equipment API', 'Failed view attempt due to excessive length of serial number.');
    exit;
}

$serial_number = mysqli_real_escape_string($dblink, $serial_number);

// SQL to get equipment details
$sql = "SELECT s.serial_number, s.status, d.device_type, m.manufacturer_name 
        FROM serials s 
        JOIN devices d ON s.device_id = d.device_id 
        JOIN manufacturers m ON s.manufacturer_id = m.manufacturer_id 
        WHERE s.serial_number = '$serial_number'";

$result = $dblink->query($sql);
//check if there is a result for the serial number
if ($result && $result->num_rows > 0) {//if successfully found
    $row = $result->fetch_assoc();
    $response['Status'] = 'SUCCESS';
    $response['Data'] = [
        'serial_number' => $row['serial_number'],
        'device_type' => $row['device_type'],
        'manufacturer_name' => $row['manufacturer_name'],
        'status' => $row['status'] 
    ];
    log_action('View Equipment API', "Successfully viewed details for serial number: $serial_number");
} else {
    $response = [// if no equipment found
        'Status' => 'ERROR',
        'MSG' => 'No equipment found with the provided serial number.',
        'Action' => 'Verify_serial_number'
    ];
    log_action('View Equipment API', "No results found for serial number: $serial_number");
}

echo json_encode($response);
?>
