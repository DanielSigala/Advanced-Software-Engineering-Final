<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Modify Equipment</title>
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
                    <span class="icon icon-bar"></span>
                    <span class="icon icon-bar"></span>
                </button>
                <a href="#" class="navbar-brand">Modify Equipment</a>
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
    <section id="home"></section>
    <section id="feature" class="container">
        <h2>Modify Equipment</h2>
        <?php
        include("../db_connect.php");
        require_once("../log_function.php");
        $dblink = db_connect();
		//gets device from the id passed in the link to this endpoint
        $current = ['device_id' => '', 'manufacturer_id' => '', 'serial_number' => ''];
        if (isset($_GET['serial_id'])) {
            $serial_id = intval($_GET['serial_id']);
            $query = "SELECT * FROM serials WHERE serial_id = $serial_id";
            $result = $dblink->query($query);
            if ($result) {
                $current = $result->fetch_assoc();
            }
        }
		
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['serial_id'])) {
            $serial_id = intval($_POST['serial_id']);
            $device_id = $_POST['device'];
            $manufacturer_id = $_POST['manufacturer'];
            $serialNumber = $dblink->real_escape_string($_POST['serialNumber']);

            $updateSql = "UPDATE serials SET device_id = '$device_id', manufacturer_id = '$manufacturer_id', serial_number = '$serialNumber' WHERE serial_id = $serial_id";
            if ($dblink->query($updateSql)) {
                log_action('Modify Equipment', "Equipment modified for serial ID: $serial_id");
                echo "<p>Equipment details modified successfully.</p>";
                header("Location: index.php?msg=EquipmentModified");
                exit;
            } else {
				//logs if there was an error modifying equipment
                log_action('Modify Equipment Error', "Failed to modify equipment for serial ID: $serial_id - " . $dblink->error);
                echo "<p>Error updating equipment details: " . $dblink->error . "</p>";
            }
        }

        // Load devices and manufacturers for dropdowns
        $devices = $dblink->query("SELECT device_id, device_type FROM devices");
        $manufacturers = $dblink->query("SELECT manufacturer_id, manufacturer_name FROM manufacturers");
        ?>
        <form action="modify_equipment.php" method="post">
            <div class="form-group">
                <label for="device">Device Type:</label>
                <select class="form-control" id="device" name="device">
                    <?php while ($row = $devices->fetch_assoc()) {
                        echo "<option value='{$row['device_id']}'" . ($current['device_id'] == $row['device_id'] ? " selected" : "") . ">{$row['device_type']}</option>";
                    } ?>
                </select>
            </div>
            <div class="form-group">
                <label for="manufacturer">Manufacturer:</label>
                <select class="form-control" id="manufacturer" name="manufacturer">
                    <?php while ($row = $manufacturers->fetch_assoc()) {
                        echo "<option value='{$row['manufacturer_id']}'" . ($current['manufacturer_id'] == $row['manufacturer_id'] ? " selected" : "") . ">{$row['manufacturer_name']}</option>";
                    } ?>
                </select>
            </div>
            <div class="form-group">
                <label for="serialNumber">Serial Number:</label>
                <input type="text" class="form-control" id="serialNumber" name="serialNumber" value="<?php echo htmlspecialchars($current['serial_number']); ?>">
            </div>
            <input type="hidden" name="serial_id" value="<?php echo $serial_id; ?>">
            <button type="submit" class="btn btn-primary">Modify Equipment</button>
			<a href="modify_device.php?serial_id=<?= $serial_id ?>" class="btn btn-info">Modify Device Name</a>
			<a href="modify_manufacturer.php?serial_id=<?= $serial_id ?>" class="btn btn-warning">Modify Manufacturer Name</a>
    </section>
        </form>
    </section>
</body>
</html>
