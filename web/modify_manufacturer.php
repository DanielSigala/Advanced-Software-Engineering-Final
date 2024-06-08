<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Modify Manufacturer Type</title>
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
                    <span class="icon icon-bar"></span>
                </button>
                <a href="#" class="navbar-brand">Modify Manufacturer Type</a>
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
            <h2>Modify Manufacturer Type Name</h2>
            <?php
            include("../db_connect.php");
            require_once("../log_function.php");
            $dblink = db_connect();

  
           $serial_id = isset($_GET['serial_id']) ? $_GET['serial_id'] : '';


            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['manufacturer_id'], $_POST['manufacturer_name'])) {
                $manufacturer_id = intval($_POST['manufacturer_id']);
                $manufacturer_name = trim($_POST['manufacturer_name']);
                $manufacturer_name = $dblink->real_escape_string($manufacturer_name);

                if (!empty($manufacturer_name)) {
                    $updateSql = "UPDATE manufacturers SET manufacturer_name = '$manufacturer_name' WHERE manufacturer_id = $manufacturer_id";
                    if ($dblink->query($updateSql)) {
						echo "<div class='alert alert-success'>Manufacturer name updated successfully.</div>";
						log_action('Modify Manufacturer', "Manufacturer type updated for manufacturer ID: $manufacturer_id");
						echo "<meta http-equiv='refresh' content='3;url=modify_equipment.php?serial_id=$serial_id'>";
					} else {
                        echo "<div class='alert alert-danger'>Error updating manufacturer name: " . $dblink->error . "</div>";
                        log_action('Modify Manufacturer Error', "Failed to update manufacturer name for Manufacturer ID: $manufacturer_id - " . $dblink->error);
                    }
                } else {
                    echo "<div class='alert alert-danger'>Manufacturer name cannot be empty.</div>";
                    log_action('Modify Manufacturer Error', "Attempt to update manufacturer with empty name for Manufacturer ID: $manufacturer_id");
                }
            }

            $manufacturers = $dblink->query("SELECT manufacturer_id, manufacturer_name FROM manufacturers");
            ?>

            <form action="modify_manufacturer.php?serial_id=<?php echo htmlspecialchars($serial_id); ?>" method="post">
                <div class="form-group">
                    <label for="manufacturer_id">Select Manufacturer:</label>
                    <select class="form-control" id="manufacturer_id" name="manufacturer_id">
                        <?php while ($row = $manufacturers->fetch_assoc()) {
                            echo "<option value='{$row['manufacturer_id']}'" . ($row['manufacturer_id'] == $manufacturer_id ? " selected" : "") . ">{$row['manufacturer_name']}</option>";
                        } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="manufacturer_name">New Manufacturer Name:</label>
                    <input type="text" class="form-control" id="manufacturer_name" name="manufacturer_name" required>
                </div>
                <button type="submit" class="btn btn-primary">Update Manufacturer Name</button>
            </form>
        </div>
    </div>
</section>
</body>
</html>
