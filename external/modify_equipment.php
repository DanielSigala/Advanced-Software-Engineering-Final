<?php
function makeApiRequest($url, $postData = '') {//function to make api call given a url and data to post. helps because we have a lot of calls
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

    if (!empty($postData)) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/x-www-form-urlencoded',
            'Content-Length: ' . strlen($postData)
        ));
    }

    $response = curl_exec($ch);
    if (curl_errno($ch)) {//if api call returns error.
        echo "<div class='alert alert-danger'>cURL error: " . curl_error($ch) . "</div>";
        return [];
    }

    curl_close($ch);
    return json_decode($response, true);
}

// get device and manufacturer lists for dropdowns
$devicesResponse = makeApiRequest("https://ec2-3-142-142-169.us-east-2.compute.amazonaws.com:8080/api/list_devices.php");
$manufacturersResponse = makeApiRequest("https://ec2-3-142-142-169.us-east-2.compute.amazonaws.com:8080/api/list_manufacturers.php");

$devices = isset($devicesResponse['Data']) ? $devicesResponse['Data'] : [];
$manufacturers = isset($manufacturersResponse['Data']) ? $manufacturersResponse['Data'] : [];
//checks if serial number, device type, and manufacturer name were passed in as parameters usually for when you view an eqipment and click "modify equipment"
$serial_number = isset($_GET['serial_number']) ? $_GET['serial_number'] : '';
//$selected_device_type = isset($_GET['device_type']) ? $_GET['device_type'] : '';
//$selected_manufacturer_name = isset($_GET['manufacturer_name']) ? $_GET['manufacturer_name'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // array that holds the POST data
    $postData = [
        'serial_number' => $_POST['serial_number'],
        'new_serial_number' => $_POST['new_serial_number'],
    ];

    // add new_device_type to $postData if it's not empty
    if (!empty($_POST['new_device_type'])) {
        $postData['new_device_type'] = $_POST['new_device_type'];
    }

    // add new_manufacturer_name to $postData if it's not empty
    if (!empty($_POST['new_manufacturer_name'])) {
        $postData['new_manufacturer_name'] = $_POST['new_manufacturer_name'];
    }

    // add new_status to $postData if it's not empty
    if (!empty($_POST['new_status'])) {
        $postData['new_status'] = $_POST['new_status'];
    }

    $postDataQuery = http_build_query($postData);

    // make the API call
    $apiUrl = "https://ec2-3-142-142-169.us-east-2.compute.amazonaws.com:8080/api/modify_equipment.php";
    $response = makeApiRequest($apiUrl, $postDataQuery);

    if (!empty($response['Status'])) {//if successful or unsuccesful response
        echo "<div class='alert alert-" . ($response['Status'] === 'SUCCESS' ? 'success' : 'danger') . "'>" . $response['MSG'] . "</div>";
    } else {//if no response
        echo "<div class='alert alert-danger'>Failed to get a valid response from the API.</div>";
    }
}
?>


<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Advanced Software Engineering</title>
    <link href="../assets/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="../assets/css/owl.carousel.css">
    <link rel="stylesheet" href="../assets/css/owl.theme.default.min.css">
    <link rel="stylesheet" href="../assets/css/templatemo-style.css">
	<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body id="top" data-spy="scroll" data-target=".navbar-collapse" data-offset="50">
    <section class="navbar custom-navbar navbar-fixed-top" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="icon icon-bar"></span>
                    <span the class="icon icon-bar"></span>
                    <span the class="icon icon-bar"></span>
                </button>
                <a href="#" class="navbar-brand">Modify Equipment</a>
            </div>
            <div class="collapse navbar-collapse">
			<ul class="nav navbar-nav navbar-nav-first">
				<li><a href="index.php" class="smoothScroll">Home</a></li>
				<li><a href="search.php" class="smoothScroll">Search Equipment</a></li>	
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">Add Equipment <b class="caret"></b></a>
					<ul class="dropdown-menu">
						<li><a href="add_equipment.php" class="smoothScroll">Add Equipment</a></li>
						<li><a href="add_device.php" class="smoothScroll">Add Device</a></li>
						<li><a href="add_manufacturer.php" class="smoothScroll">Add Manufacturer</a></li>
					</ul>
				</li>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">Modify Equipment <b class="caret"></b></a>
					<ul class="dropdown-menu">
						<li><a href="modify_equipment.php" class="smoothScroll">Modify Equipment</a></li>
						<li><a href="modify_device.php" class="smoothScroll">Modify Device</a></li>
						<li><a href="modify_manufacturer.php" class="smoothScroll">Modify Manufacturer</a></li>
					</ul>
				</li>
			</ul>
		</div>
        </div>
    </section>
    <section id="home"></section>
    <section id="feature" class="container">
        <form method="post" action="">
            <div class="form-group">
				<label>New Device Type:</label>
				<select class="form-control" name="new_device_type">
					<option value="">Select Device Type</option> <!-- Default option added -->
					<?php foreach ($devices as $device): ?>
						<option value="<?= htmlspecialchars($device['device_type']) ?>" <?= isset($selected_device_type) && $selected_device_type == $device['device_type'] ? 'selected' : '' ?>>
							<?= htmlspecialchars($device['device_type']) ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="form-group">
				<label>New Manufacturer Name:</label>
				<select class="form-control" name="new_manufacturer_name">
					<option value="">Select Manufacturer</option> <!-- Default option added -->
					<?php foreach ($manufacturers as $manufacturer): ?>
						<option value="<?= htmlspecialchars($manufacturer['manufacturer_name']) ?>" <?= isset($selected_manufacturer_name) && $selected_manufacturer_name == $manufacturer['manufacturer_name'] ? 'selected' : '' ?>>
							<?= htmlspecialchars($manufacturer['manufacturer_name']) ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>
            <div class="form-group">
                <label>Serial Number:</label><!-- takes in the serial number and is a required field-->
                <input type="text" class="form-control" name="serial_number" required value="<?= htmlspecialchars($serial_number) ?>">
            </div>
			<div class="form-group">
				<label>New Serial Number:</label>
				<input type="text" class="form-control" name="new_serial_number" value="">
			</div>
            <div class="form-group">
                <label>New Status:</label>
                <select class="form-control" name="new_status">
					<option value="">Select Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Modify Equipment</button>
        </form>
    </section>
</body>
</html>