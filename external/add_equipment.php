<?php
function makeApiRequest($url, $postData = '') {
	// intialize curl using link for my api
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	
	//fill in the posted data to send to the api
    if (!empty($postData)) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/x-www-form-urlencoded',
            'Content-Length: ' . strlen($postData)
        ));
    }
	// call the api with curl
	$response = curl_exec($ch);
    if (curl_errno($ch)) {
        echo "<div class='alert alert-danger'>cURL error: " . curl_error($ch) . "</div>";
        return [];
    }

    curl_close($ch);
    return json_decode($response, true);
}

//make api calls to list_devices and list_manufacturers to populate dropdown menu
$deviceApiResponse = makeApiRequest("https://ec2-3-142-142-169.us-east-2.compute.amazonaws.com:8080/api/list_devices.php");
$manufacturerApiResponse = makeApiRequest("https://ec2-3-142-142-169.us-east-2.compute.amazonaws.com:8080/api/list_manufacturers.php");

$devices = isset($deviceApiResponse['Data']) ? $deviceApiResponse['Data'] : [];
$manufacturers = isset($manufacturerApiResponse['Data']) ? $manufacturerApiResponse['Data'] : [];

//when the user clicks the button it triggers the post
if (isset($_POST['submit'])) {
    $postData = http_build_query([
        'device_type' => $_POST['device_type'],  
        'manufacturer_name' => $_POST['manufacturer_name'],
        'serial_number' => $_POST['serial_number'],
    ]);

    $apiUrl = "https://ec2-3-142-142-169.us-east-2.compute.amazonaws.com:8080/api/add_equipment.php";
    $responseArray = makeApiRequest($apiUrl, $postData);

    // Display response from the API
    if (!empty($responseArray)) {
        echo "<div class='alert alert-" . ($responseArray['Status'] === 'SUCCESS' ? 'success' : 'danger') . "'>" . $responseArray['MSG'] . "</div>";
    } else {
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
	<!-- some css bootstrap files that are used for the dropdown menu -->
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
                <a href="#" class="navbar-brand">Add New Equipment</a>
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
        <div class="row">
		<form method="post" action="">
			<!-- form used to post data to api -->
			<div class="form-group">
				<label for="exampleDevice">Device Type:</label>
				<select class="form-control" name="device_type">
					<!-- list the devices gotten from the api call in a dropdown menu when posted passes type-->
					<?php foreach ($devices as $device) {
						echo "<option value='{$device['device_type']}'>{$device['device_type']}</option>";
					} ?>
				</select>
			</div>
			<div class="form-group">
				<label for="exampleManufacturer">Manufacturer Name:</label>
				<select class="form-control" name="manufacturer_name">
					<!-- list the manufacturers gotten from the api call in a dropdown menu when posted passes name-->
					<?php foreach ($manufacturers as $manufacturer) {
						echo "<option value='{$manufacturer['manufacturer_name']}'>{$manufacturer['manufacturer_name']}</option>";
					} ?>
				</select>
			</div>
			<div class="form-group">
				<label for="exampleSerial">Serial Number:</label>
				<input type="text" class="form-control" id="serialInput" name="serial_number">
			</div>
			<button type="submit" class="btn btn-primary" name="submit" value="submit">Add Equipment</button>
			<a href="add_device.php" class="btn btn-info" style="margin-right: 10px;">Add New Device</a>
            <a href="add_manufacturer.php" class="btn btn-warning" style="margin-right: 10px;">Add New Manufacturer</a>
		</form>
        </div>
    </section>
</body>
</html>
