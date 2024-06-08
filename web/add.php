<?php
//include the db connect file and the log function file
include("../db_connect.php");
require_once("../log_function.php");
$dblink = db_connect();

if (isset($_POST['submit'])) {
    $device = $_POST['device'];
    $manufacturer = $_POST['manufacturer'];
    $serialNumber = trim($_POST['serialnumber']);

    // ke sure that the length isn't greater than the longest serial number in the db
    if (strlen($serialNumber) > 67) {
        echo "<div class='alert alert-danger' role='alert'>Serial number must not exceed 67 characters.</div>";
        log_action('Validation Error', "Serial number length exceeded: $serialNumber");
    } else {
        $sql = "SELECT `serial_id` FROM `serials` WHERE `serial_number` = '$serialNumber'";
        $rst = $dblink->query($sql);

        if (!$rst) {
            die("<p>Something went wrong with $sql<br>" . $dblink->error);
        }
		//if the serial number is already in the db log the error 
        if ($rst->num_rows > 0) { 
            log_action('Duplicate Serial', "Attempt to add equipment with existing serial number: $serialNumber");
            header("Location: add.php?msg=DeviceExists");
            exit;
        } else {//add the equipment once serial is confirmed to be unique
            $sql = "INSERT INTO `serials` (`device_id`, `manufacturer_id`, `serial_number`) VALUES ('$device', '$manufacturer', '$serialNumber')";
            if ($dblink->query($sql)) {//log that the equipment was added
                log_action('Add Equipment', "Added new equipment with serial number: $serialNumber");
                header("Location: index.php?msg=EquipmentAdded");
                exit;
            } else {
                die("<p>Something went wrong with $sql<br>" . $dblink->error);
            }
        }
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
</head>
<body id="top" data-spy="scroll" data-target=".navbar-collapse" data-offset="50">
    <!-- MENU -->
    <section class="navbar custom-navbar navbar-fixed-top" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="icon icon-bar"></span>
                    <span the class="icon icon-bar"></span>
                    <span class="icon icon-bar"></span>
                </button>
                <a href="#" class="navbar-brand">Add New Equipment</a>
            </div>
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
            <div class="row">
                <?php 
				//sql used to make device dropdown list
                $sql = "SELECT `device_type`, `device_id` FROM `devices`";
                $result = $dblink->query($sql);
                $devices = [];
                $manufacturers = [];
                if ($result) {
                    while ($data = $result->fetch_assoc()) {
                        $devices[$data['device_id']] = $data['device_type'];
                    }
                }
				//sql used to make manufacturer dropdown list
                $sql = "SELECT `manufacturer_name`, `manufacturer_id` FROM `manufacturers`";
                $result = $dblink->query($sql);
                if ($result) {
                    while ($data = $result->fetch_assoc()) {
                        $manufacturers[$data['manufacturer_id']] = $data['manufacturer_name'];
                    }
                }
				//if the header message is DeviceExists then display an alert to the user
                if (isset($_REQUEST['msg']) && $_REQUEST['msg'] == "DeviceExists") {
                    echo '<div class="alert alert-danger" role="alert">Serial Number already exists in database!</div>';
                }
                ?>
                <form method="post" action="">
                    <div class="form-group">
                        <label for="exampleDevice">Device:</label>
                        <select class="form-control" name="device">
                            <?php foreach ($devices as $id => $type) {
                                echo "<option value='$id'>$type</option>";
                            } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="exampleManufacturer">Manufacturer:</label>
                        <select class="form-control" name="manufacturer">
                            <?php foreach ($manufacturers as $id => $name) {
                                echo "<option value='$id'>$name</option>";
                            } ?>
						</select>
                    </div>
                    <div class="form-group">
                        <label for="exampleSerial">Serial Number:</label>
                        <input type of="text" class="form-control" id="serialInput" name="serialnumber">
                    </div>
                    <button type="submit" class="btn btn-primary" name="submit" value="submit">Add Equipment</button>
                    <a href="add_device.php" class="btn btn-info" style="margin-right: 10px;">Add New Device</a>
                    <a href="add_manufacturer.php" class="btn btn-warning" style="margin-right: 10px;">Add New Manufacturer</a>
                </form>
            </div>
        </div>
    </section>
</body>
</html>
