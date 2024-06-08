<?php
require_once("../db_connect.php");
require_once("../log_function.php");
header('Content-Type: application/json');

$dblink = db_connect();

// query to select manufacturer_id manufacturer_name and status from manufacturers
$sql = "SELECT manufacturer_id, manufacturer_name, status FROM manufacturers";
$result = $dblink->query($sql);

$manufacturers = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        // add manufacturers to manufacturers[] table
        $manufacturers[] = [
            'manufacturer_id' => $row['manufacturer_id'],
            'manufacturer_name' => $row['manufacturer_name'],
            'status' => $row['status'] 
        ];
    }
    // put the manufacturers as the data in the response
    $response = [
        'Status' => 'SUCCESS',
        'Data' => $manufacturers,
        'Action' => 'none'
    ];
    echo json_encode($response);
    log_action('List Manufacturers', 'Successfully retrieved all active manufacturers');
} else {
    // if the query fails	
    $response = [
        'Status' => 'ERROR',
        'MSG' => 'Failed to retrieve manufacturers.',
        'Action' => 'Database_query_failure'
    ];
    echo json_encode($response);
    log_action('List Manufacturers Error', 'Failed to retrieve manufacturers: ' . $dblink->error);
}
?>
