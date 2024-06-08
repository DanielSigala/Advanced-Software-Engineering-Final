<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>View Equipment Details</title>
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
                    <span the class="icon icon-bar"></span>
                </button>
                <!-- LOGO TEXT HERE -->
                <a href="#" class="navbar-brand">View Equipment Details</a>
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
    <section id="feature" class="container">
        <h2>Equipment Details</h2>
        <?php
        include("../db_connect.php");
        require_once("../log_function.php");
        if (isset($_GET['serial_id'])) {
            $serial_id = intval($_GET['serial_id']); // make sure the ID is treated as an integer
            $dblink = db_connect();
			//select equipment which corresponds to the serial id
            $sql = "SELECT s.serial_number, d.device_type, m.manufacturer_name
                    FROM serials s
                    JOIN devices d ON s.device_id = d.device_id
                    JOIN manufacturers m ON s.manufacturer_id = m.manufacturer_id
                    WHERE s.serial_id = $serial_id";
			//echos the information of the given equipment
            $result = $dblink->query($sql);
            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                echo "<div class='well'><p><strong>Serial Number:</strong> " . htmlspecialchars($row['serial_number']) . "</p>";
                echo "<p><strong>Device Type:</strong> " . htmlspecialchars($row['device_type']) . "</p>";
                echo "<p><strong>Manufacturer:</strong> " . htmlspecialchars($row['manufacturer_name']) . "</p></div>";
                echo "<a href='modify_equipment.php?serial_id=" . $serial_id . "' class='btn btn-primary'>Modify Equipment</a>";
                // log the view action
                log_action('View Equipment', "Viewed details for serial ID: $serial_id");
            } else {
				log_action('View Equipment', "No details for serial ID: $serial_id");
                echo "<p>No equipment found for ID $serial_id.</p>";
            }
        } else {
			//log if unsuccessful
			log_action('View Equipment', "No equipment ID provided");
            echo "<p>No equipment ID provided.</p>";
        }
		
        ?>
    </section>
</body>
</html>
