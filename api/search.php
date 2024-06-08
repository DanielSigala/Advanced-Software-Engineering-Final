<?php
require_once("../db_connect.php");
require_once("../log_function.php");

header('Content-Type: application/json');
$dblink = db_connect();

// initialize response array
$response = [];

$searchType = isset($_GET['searchType']) ? $_GET['searchType'] : '';
$searchQuery = isset($_GET['searchQuery']) ? $_GET['searchQuery'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : 'active';

// check if query is empty
if (empty($searchQuery)) {
    $response = [
        'Status' => 'ERROR',
        'MSG' => 'Search query cannot be empty.',
        'Action' => 'None'
    ];
    echo json_encode($response);
    log_action('Search Equipment API', 'Failed search attempt due to empty query.');
    exit;
}
// make sure query type is valid
if (!in_array($searchType, ['device', 'manufacturer', 'serial_number'])) {
    $response = [
        'Status' => 'ERROR',
        'MSG' => 'Invalid search query type.',
        'Action' => 'validate_search_query_type'
    ];
    echo json_encode($response);
    log_action('Search Equipment API', 'Failed search attempt due to invalid search query type.');
    exit;
}

// validate status
if (!in_array($status, ['active', 'inactive', 'all'])) {
    $response = [
        'Status' => 'ERROR',
        'MSG' => 'Invalid status parameter.',
        'Action' => 'None'
    ];
    echo json_encode($response);
    log_action('Search Equipment API', 'Failed search attempt due to invalid status parameter.');
    exit;
}

//check length of different searches 
if (($searchType === 'device' || $searchType === 'manufacturer') && strlen($searchQuery) > 64) {
    $response = [
        'Status' => 'ERROR',
        'MSG' => 'Search query exceeds maximum length of 64 characters for ' . $searchType,
        'Action' => 'validate_query_length'
    ];
    echo json_encode($response);
    log_action('Search Equipment API', 'Search query exceeds maximum length for ' . $searchType);
    exit;
}

if ($searchType === 'serial_number' && strlen($searchQuery) > 67) {
    $response = [
        'Status' => 'ERROR',
        'MSG' => 'Search query exceeds maximum length of 67 characters for serial number',
        'Action' => 'validate_query_length'
    ];
    echo json_encode($response);
    log_action('Search Equipment API', 'Search query exceeds maximum length for serial number');
    exit;
}

// make SQL query based on search type
$sql = "";
$searchQueryEscaped = mysqli_real_escape_string($dblink, $searchQuery);
$statusCondition = $status !== 'all' ? "AND s.status = '$status'" : "";

switch ($searchType) {
    case 'device':
        $sql = "SELECT s.serial_id, s.serial_number, d.device_type, m.manufacturer_name 
                FROM devices d 
                JOIN serials s ON d.device_id = s.device_id 
                JOIN manufacturers m ON s.manufacturer_id = m.manufacturer_id 
                WHERE d.device_type LIKE '%$searchQueryEscaped%' 
                $statusCondition
                LIMIT 1000";
        break;
    case 'manufacturer':
        $sql = "SELECT s.serial_id, s.serial_number, d.device_type, m.manufacturer_name 
                FROM manufacturers m 
                JOIN serials s ON m.manufacturer_id = s.manufacturer_id 
                JOIN devices d ON s.device_id = d.device_id 
                WHERE m.manufacturer_name LIKE '%$searchQueryEscaped%' 
                $statusCondition
                LIMIT 1000";
        break;
    case 'serial_number':
        // use exact match for performance
        $sql = "SELECT s.serial_id, s.serial_number, d.device_type, m.manufacturer_name 
                FROM serials s 
                JOIN devices d ON s.device_id = d.device_id 
                JOIN manufacturers m ON s.manufacturer_id = m.manufacturer_id 
                WHERE s.serial_number = '$searchQueryEscaped' 
                $statusCondition
                LIMIT 1000";
        break;
    default:
        $response = [
            'Status' => 'ERROR',
            'MSG' => 'Invalid search type.',
            'Action' => 'None'
        ];
        echo json_encode($response);
        log_action('Search Equipment API', 'Failed search attempt due to invalid search type.');
        exit;
}
// execute SQL query
$result = $dblink->query($sql);

// get and return results
$equipments = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $equipments[] = $row;
    }
    $response = [//if search is successful
        'Status' => 'SUCCESS',
        'Data' => $equipments,
		'Action' => 'none'
    ];
} else {
    $response = [//if no results found from search
        'Status' => 'ERROR',
        'MSG' => 'No results found.',
        'Action' => 'search_again'
    ];
    log_action('Search Equipment API', "No results found for search type: $searchType and query: $searchQuery");
}

echo json_encode($response);
?>
