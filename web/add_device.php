<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Advanced Software Engineering</title>
    <link href="../assets/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="../assets/css/owl.carousel.css">
    <link rel="stylesheet" href="../assets/css/owl.theme.default.min.css">
    <!-- MAIN CSS -->
    <link rel="stylesheet" href="../assets/css/templatemo-style.css">
</head>
<body id="top" data-spy="scroll" data-target=".navbar-collapse" data-offset="50">
    <!-- MENU -->
    <section class="navbar custom-navbar navbar-fixed-top" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="icon icon-bar"></span>
                    <span class="icon icon-bar"></span>
                    <span the class="icon icon-bar"></span>
                </button>
                <!-- LOGO TEXT HERE -->
                <a href="#" class="navbar-brand">Add New Device</a>
            </div>
            <!-- MENU LINKS -->
            <div class="collapse navbar-collapse">
                <ul class="nav navbar-nav navbar-nav-first">
                    <li><a href="index.php" class="smoothScroll">Home</a></li>
                    <li><a href="search.php" class="smoothScroll">Search Equipment</a></li>
                    <li><a href="add.php" class="smoothScroll">Add Equipment</a></li>
                </ul>
            </div>
        </div>
    </section>
    <!-- HOME -->
    <section id="home">
    </section>
    <!-- FEATURE -->
    <section id="feature">
        <div class="container">
            <h2>Add New Device Type</h2>
            <?php
			include("../db_connect.php");
			require_once("../log_function.php");
			$dblink = db_connect();

			if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['deviceType'])) {
				$deviceType = trim($_POST['deviceType']);

				// Make sure the input is a varchar length 64 or less if not log error into db
				if (!is_string($deviceType) || strlen($deviceType) > 64) {
					echo "<div class='alert alert-danger' role='alert'>Error: Device type must be a string and not exceed 64 characters.</div>";
					log_action('Add Device Error', "Invalid device type provided: $deviceType");
				} else {
					//escape the string for additional validation making sure theres no newline
					$deviceType = $dblink->real_escape_string($deviceType);

					// Check if the device type already exists
					$checkSql = "SELECT device_type FROM devices WHERE device_type = '$deviceType'";
					$checkResult = $dblink->query($checkSql);
					//log if devicd already exists
					if ($checkResult && $checkResult->num_rows > 0) {
						echo '<div class="alert alert-danger" role="alert">Device type already exists in database!</div>';
						log_action('Add Device', "Attempted to add existing device type: $deviceType");
					} else {//insert into db and log the action
						$insertSql = "INSERT INTO devices (device_type) VALUES ('$deviceType')";
						if ($dblink->query($insertSql)) {
							log_action('Add Device', "New device type added: $deviceType");
							echo "<div class='alert alert-success' role='alert'>New device type '$deviceType' added successfully.</div>";
							header("Location: index.php?msg=DeviceAdded");
							exit;
						} else {//if any sql or db errors, log into db
							echo "<div class='alert alert-danger' role='alert'>Error adding new device type: " . $dblink->error . "</div>";
							log_action('Add Device Failure', "Error adding new device: $deviceType - " . $dblink->error);
						}
					}
				}
			}
			?>
            <form action="add_device.php" method="post">
                <div class="form-group">
                    <label for="deviceType">Device Type:</label>
                    <input type="text" name="deviceType" id="deviceType" required class="form-control">
                </div>
                <button type="submit" class="btn btn-primary">Add Device Type</button>
            </form>
        </div>
    </section>
</body>
</html>
