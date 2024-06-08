<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Modify Device Type</title>
    <link href="../assets/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="../assets/css/owl.carousel.css">
    <link rel="stylesheet" href="../assets/css/owl.theme.default.min.css">
    <link rel="stylesheet" href="../assets/css/templatemo-style.css">
</head>
<body id="top" data-spy="scroll" data-target=".navbar-collapse" data-offset="50">
    <section class="navbar custom-navbar navbar-fixed-top" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="icon icon-bar"></span>
                    <span class="icon icon-bar"></span>
                    <span class "icon icon-bar"></span>
                </button>
                <a href="#" class="navbar-brand">Modify Device Type</a>
            </div>
            <div class="collapse navbar-collapse">
                <ul class="nav navbar-nav navbar-nav-first">
                    <li><a href="index.php" class="smoothScroll">Home</a></li>
                    <li><a href="search.php" class="smoothScroll">Search Equipment</a></li>
                    <li><a href="add_equipment.php" class="smoothScroll">Add Equipment</a></li>
                </ul>
            </div>
        </div>
    </section>
    <section id="home"></section>
    <section id="feature" class="container">
        <div class="row">
            <h2>Modify Device Type Name</h2>
            <?php
            include("../db_connect.php");
            require_once("../log_function.php");
            $dblink = db_connect();
			$serial_id = isset($_GET['serial_id']) ? $_GET['serial_id'] : '';

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['device_id'], $_POST['device_name'], $_POST['serial_id'])) {
                $device_id = intval($_POST['device_id']);
                $device_name = trim($_POST['device_name']);
                $serial_id = intval($_POST['serial_id']); 
                $device_name = $dblink->real_escape_string($device_name);  

                if (!empty($device_name)) {
                    $updateSql = "UPDATE devices SET device_type = '$device_name' WHERE device_id = $device_id";
                    if ($dblink->query($updateSql)) {
						echo "<div class='alert alert-success'>Device type updated successfully.</div>";
						log_action('Modify Device', "Device type updated for device ID: $device_id");
						echo "<meta http-equiv='refresh' content='3;url=modify_equipment.php?serial_id=$serial_id'>";
					} else {
                        echo "<div class='alert alert-danger'>Error updating device type: " . $dblink->error . "</div>";
                        log_action('Modify Device Error', "Failed to update device type for device ID: $device_id - " . $dblink->error);
                    }
                } else {
                    echo "<div class='alert alert-danger'>Device name cannot be empty.</div>";
                    log_action('Modify Device Error', "Attempt to update device with empty name for device ID: $device_id");
                }
            }

            $devices = $dblink->query("SELECT device_id, device_type FROM devices");
            ?>

            <form action="modify_device.php" method="post">
                <div class="form-group">
                    <label for="device_id">Select Device:</label>
                    <select class="form-control" id="device_id" name="device_id">
                        <?php while ($row = $devices->fetch_assoc()) {
                            echo "<option value='{$row['device_id']}'" . ($row['device_id'] == $device_id ? " selected" : "") . ">{$row['device_type']}</option>";
                        } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="device_name">New Device Name:</label>
                    <input type="text" class="form-control" id="device_name" name="device_name" required>
                </div>
                <input type="hidden" name="serial_id" value="<?php echo htmlspecialchars($serial_id); ?>">
                <button type="submit" class="btn btn-primary">Update Device Name</button>
            </form>
        </div>
    </div>
</section>
</body>
</html>
