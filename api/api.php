<?php
require_once("../db_connect.php");
$url = $_SERVER['REQUEST_URI'];
$path = parse_url($url, PHP_URL_PATH);
$pathComponents = explode("/", trim($path, "/"));
$endPoint = $pathComponents[1];

header('Content-Type: application/json');

switch ($endPoint) {
    case "add_equipment":
        $serial_number = isset($_REQUEST['serial_number']) ? $_REQUEST['serial_number'] : '';
		$device_type = isset($_REQUEST['device_type']) ? $_REQUEST['device_type'] : '';
		$manufacturer_name = isset($_REQUEST['manufacturer_name']) ? $_REQUEST['manufacturer_name'] : '';
        include("add_equipment.php");
        break;
    case "add_device":
		$deviceType = isset($_REQUEST['device_type']) ? $_REQUEST['device_type'] : null;
        include("add_device.php");
        break;
    case "add_manufacturer":
        $manufacturerName = isset($_REQUEST['manufacturer_name']) ? $_REQUEST['manufacturer_name'] : null;
        include("add_manufacturer.php");
        break;
    case "search":
        $searchType = isset($_REQUEST['searchType']) ? $_REQUEST['searchType'] : '';
        $searchQuery = isset($_REQUEST['searchQuery']) ? $_REQUEST['searchQuery'] : '';
        $status = isset($_REQUEST['status']) ? $_REQUEST['status'] : 'active';
        include("search.php");
        break;
    case "modify_device":
        $current_name = isset($_REQUEST['current_name']) ? $_REQUEST['current_name'] : null;
        $new_name = isset($_REQUEST['new_name']) ? $_REQUEST['new_name'] : null;
        $new_status = isset($_REQUEST['new_status']) ? $_REQUEST['new_status'] : null;
        include("modify_device.php");
        break;
    case "modify_manufacturer":
		$current_name = isset($_REQUEST['current_name']) ? $_REQUEST['current_name'] : null;
        $new_name = isset($_REQUEST['new_name']) ? $_REQUEST['new_name'] : null;
        $new_status = isset($_REQUEST['new_status']) ? $_REQUEST['new_status'] : null;
        include("modify_manufacturer.php");
        break;
    case "list_devices":
        include("list_devices.php");
        break;
    case "list_manufacturers":
        include("list_manufacturers.php");
        break;
	case "view_equipment":
        $serial_number = isset($_GET['serial_number']) ? $_GET['serial_number'] : null;
        include("view_equipment.php");
        break;
	case "modify_equipment":
        $serial_number = isset($_REQUEST['serial_number']) ? $_REQUEST['serial_number'] : '';
        $new_device_type = isset($_REQUEST['new_device_type']) ? $_REQUEST['new_device_type'] : '';
        $new_manufacturer_name = isset($_REQUEST['new_manufacturer_name']) ? $_REQUEST['new_manufacturer_name'] : '';
		$new_serial_number = isset($_REQUEST['new_serial_number']) ? $_REQUEST['new_serial_number'] : '';
        $new_status = isset($_REQUEST['new_status']) ? $_REQUEST['new_status'] : '';
        include("modify_equipment.php");
        break;
    default:
        $response = [
            'Status' => 'ERROR',
            'MSG' => 'Invalid Endpoint',
            'Action' => 'None'
        ];
        echo json_encode($response);
        break;
}

?>
