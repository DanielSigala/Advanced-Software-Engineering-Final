<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Modify Device Details</title>
    <link href="../assets/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="../assets/css/owl.carousel.css">
    <link rel="stylesheet" href="../assets/css/owl.theme.default.min.css">
    <link rel="stylesheet" href="../assets/css/templatemo-style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body id="top" data-spy="scroll" data-target=".navbar-collapse" data-offset="50">
    <section class="navbar custom-navbar navbar-fixed-top" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="icon icon-bar"></span>
                    <span class="icon icon-bar"></span>
                    <span the class="icon icon-bar"></span>
                </button>
                <a href="#" class="navbar-brand">Modify Device Details</a>
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
            <h2>Modify Device Details</h2>
            <?php
            function fetchDevices() {//get list of devices from list_devices.php api using curl
                $url = "https://ec2-3-142-142-169.us-east-2.compute.amazonaws.com:8080/api/list_devices.php";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                $response = curl_exec($ch);
                if (curl_errno($ch)) {
                    echo '<div class="alert alert-danger">cURL error: ' . curl_error($ch) . '</div>';
                    return [];
                }
                curl_close($ch);
                return json_decode($response, true);
            }
			//get list of devices for dropdown menu from fetchdevices function
            $devicesResponse = fetchDevices();
            $devices = isset($devicesResponse['Data']) ? $devicesResponse['Data'] : [];

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {//send data from fields to modify_device.php api
                $current_name = $_POST['current_name'];
                $new_name = $_POST['new_name'];
                $new_status = $_POST['new_status'];

                $postData = http_build_query([
                    'current_name' => $current_name,
                    'new_name' => $new_name,
                    'new_status' => $new_status
                ]);

                $url = "https://ec2-3-142-142-169.us-east-2.compute.amazonaws.com:8080/api/modify_device.php";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                $response = curl_exec($ch);
                if (curl_errno($ch)) {
                    echo '<div class="alert alert-danger">cURL error: ' . curl_error($ch) . '</div>';
                } else {
                    $response_data = json_decode($response, true);
                    if ($response_data['Status'] === 'SUCCESS') {//if succesful
                        echo '<div class="alert alert-success">' . htmlspecialchars($response_data['MSG']) . '</div>';
                    } else {// if failed
                        echo '<div class="alert alert-danger">' . htmlspecialchars($response_data['MSG']) . '</div>';
                    }
                }
                curl_close($ch);
            }
            ?>
			<!-- form takes in the current and new name and posts them to api -->
            <form action="" method="post">
                <div class="form-group">
                    <label for="current_name">Current Device Name:</label>
                    <select class="form-control" id="current_name" name="current_name">
                        <?php foreach ($devices as $device) {
                            echo '<option value="' . htmlspecialchars($device['device_type']) . '">' . htmlspecialchars($device['device_type']) . '</option>';
                        } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="new_name">New Device Name:</label>
                    <input type="text" class="form-control" id="new_name" name="new_name">
                </div>
                <div class="form-group">
                    <label for="new_status">New Status:</label>
                    <select class="form-control" id="new_status" name="new_status">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Update Device</button>
            </form>
        </div>
    </section>
</body>
</html>
